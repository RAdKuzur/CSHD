<?php

namespace frontend\models\work\auxiliary;

use common\components\wizards\ExcelWizard;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\repositories\dictionaries\PeopleRepository;
use frontend\services\act_participant\ActParticipantService;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class OrderEventParticipantLoader extends Model
{
    public $orderEventId;
    public $foreignEventId;
    public $file;
    public $errors = [];
    public $nomination;

    private $peopleRepository;
    private $foreignEventParticipantsRepository;
    private $actParticipantService;


    private $processed = 0;
    private $skipped = 0;
    private $totalRows = 0;

    public function __construct(
        int $orderEventId,
        int $foreignEventId,
        PeopleRepository $peopleRepository,
        ForeignEventParticipantsRepository $foreignEventParticipantsRepository,
        ActParticipantService $actParticipantService,
        $config = []
    ) {
        $this->orderEventId = $orderEventId;
        $this->foreignEventId = $foreignEventId;
        $this->peopleRepository = $peopleRepository;
        $this->foreignEventParticipantsRepository = $foreignEventParticipantsRepository;
        $this->actParticipantService = $actParticipantService;
        
        parent::__construct($config);
    }

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls, xlsx'],
            [['nomination'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Excel-файл',
            'nomination' => 'Номинация',
        ];
    }

    /**
     * Основной метод обработки файла
     * @return array Результаты импорта
     */
    public function processFile()
    {
        $this->processed = 0;
        $this->skipped = 0;
        $this->errors = [];
        
        if (!$this->validate()) {
            $this->errors[] = 'Ошибка валидации файла';
            return $this->getResults();
        }

        $filePath = $this->saveUploadedFile();
        if (!$filePath) {
            $this->errors[] = 'Не удалось сохранить файл';
            return $this->getResults();
        }
        
        try {
            $data = $this->readExcelData($filePath);
            if (empty($data)) {
                $this->errors[] = 'Файл не содержит данных или имеет неверный формат';
                return $this->getResults();
            }
            
            $this->totalRows = count($data['Фамилия'] ?? []);
            if ($this->totalRows === 0) {
                $this->errors[] = 'В файле нет данных для импорта';
                return $this->getResults();
            }
            
            $this->processAllRows($data);
            
        } catch (\Exception $e) {
            $this->errors[] = 'Ошибка при обработке файла: ' . $e->getMessage();
            Yii::error($e->getMessage(), __METHOD__);
        } finally {
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
        
        return $this->getResults();
    }

    /**
     * Сохраняет загруженный файл во временную директорию
     * @return string|null Путь к сохраненному файлу
     */
    private function saveUploadedFile()
    {
        $this->file = UploadedFile::getInstance($this, 'file');
        if (!$this->file) {
            return null;
        }
        
        $tempPath = Yii::getAlias('@runtime/temp/');
        if (!file_exists($tempPath)) {
            mkdir($tempPath, 0777, true);
        }
        
        $filename = uniqid('import_', true) . '.' . $this->file->extension;
        $filePath = $tempPath . $filename;
        
        if ($this->file->saveAs($filePath)) {
            return $filePath;
        }
        
        return null;
    }
    
    /**
     * Чтение данных из Excel файла
     * @param string $filePath Путь к файлу
     * @return array Данные из Excel
     */
    private function readExcelData($filePath)
    {
        $columns = [
            'Фамилия', 
            'Имя', 
            'Отчество', 
            'Отделы',
            'ФИО первого педагога',
            'Фио второго педагога (при необходимости)',
            'Направленность',
            'Форма реализации'
        ];
        
        return ExcelWizard::getDataFromColumns($filePath, $columns);
    }
    
    /**
     * Обработка всех строк из данных Excel
     * @param array $data Данные из Excel
     */
    private function processAllRows($data)
    {
        for ($i = 0; $i < $this->totalRows; $i++) {
            $this->processSingleRow($i, $data);
        }
    }
    
    /**
     * Обработка одной строки
     * @param int $index Индекс строки (0-based)
     * @param array $data Данные из Excel
     */
    private function processSingleRow($index, $data)
    {
        if (!$this->validateRowData($index, $data)) {
            return;
        }
        
        try {
            // 1. Поиск участника
            $participantId = $this->findParticipant(
                $data['Имя'][$index],
                $data['Фамилия'][$index],
                $data['Отчество'][$index]
            );
            
            if (!$participantId) {
                $this->logError($index, 
                    "Участник не найден: {$data['Фамилия'][$index]} {$data['Имя'][$index]} {$data['Отчество'][$index]}"
                );
                return;
            }
            
            $teacher1FullName = trim($data['ФИО первого педагога'][$index] ?? '');
            $teacher1Id = $this->findTeacher($teacher1FullName);
            
            if (!$teacher1Id) {
                $this->logError($index, 
                    "Первый педагог не найден: {$teacher1FullName}"
                );
                return;
            }
            
            $teacher2Id = null;
            $teacher2FullName = trim($data['Фио второго педагога (при необходимости)'][$index] ?? '');
            
            if (!empty($teacher2FullName)) {
                $teacher2Id = $this->findTeacher($teacher2FullName);

                if (!$teacher2Id) {
                    $this->logError($index, 
                        "Второй педагог не найден: {$teacher2FullName}"
                    );
                    return;
                }
            }
            
            $branchIds = $this->parseBranches($data['Отделы'][$index] ?? '');
            $focusId = \common\components\dictionaries\base\FocusDictionary::getByName($data['Направленность'][$index] ?? '');
            $formId = \common\components\dictionaries\base\EventWayDictionary::getByName($data['Форма реализации'][$index] ?? '');
            
            if (empty($branchIds)) {
                $this->logError($index, "Не указаны или неверно указаны отделы: " . ($data['Отделы'][$index] ?? 'пусто'));
                return;
            }
            
            if (!$focusId) {
                $this->logError($index, "Неверно указана направленность: " . ($data['Направленность'][$index] ?? 'пусто'));
                return;
            }
            
            if (!$formId) {
                $this->logError($index, "Неверно указана форма реализации: " . ($data['Форма реализации'][$index] ?? 'пусто'));
                return;
            }

            $actData = $this->prepareActData(
                $participantId,
                $teacher1Id,
                $teacher2Id,
                $focusId,
                $formId,
                $branchIds
            );

            $this->createActParticipant($actData);
        
            $this->processed++; // Успешно создан
            
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'уже есть') !== false) {
                $this->logDuplicate($index, $e->getMessage());
            } else {
                $this->logError($index, "Ошибка: " . $e->getMessage());
            }
        }
    }
    private function logDuplicate($rowNum, $message)
    {
        $duplicateMessage = "Строка " . ($rowNum + 1) . ": " . $message;
        $this->errors[] = "Внимание!" . $duplicateMessage;
        Yii::info($duplicateMessage, __METHOD__);
    }

    private function validateRowData($index, $data)
    {
        $requiredFields = [
            'Фамилия' => $data['Фамилия'][$index] ?? '',
            'Имя' => $data['Имя'][$index] ?? '',
            'Отделы' => $data['Отделы'][$index] ?? '',
            'ФИО первого педагога' => $data['ФИО первого педагога'][$index] ?? '',
            'Направленность' => $data['Направленность'][$index] ?? '',
            'Форма реализации' => $data['Форма реализации'][$index] ?? ''
        ];
        
        foreach ($requiredFields as $fieldName => $value) {
            if (empty(trim($value))) {
                $this->logError($index, "Не заполнено обязательное поле: {$fieldName}");
                return false;
            }
        }
        
        return true;
    }
    
     private function findParticipant($firstname, $surname, $patronymic)
    {

        $participant = $this->foreignEventParticipantsRepository->findByFullName(
            $firstname,
            $surname,
            $patronymic
        );
        
        return $participant ? $participant->id : null;
    }
    
    private function findTeacher($fullName)
    {
        if (empty($fullName)) {
            return null;
        }

        $parts = array_map('trim', explode(' ', $fullName));
        
        if (count($parts) !== 3) {
            return null;
        }
        
        $teacher = $this->peopleRepository->findByFio($parts[1], $parts[0], $parts[2]);
        
        return $teacher ? $teacher->id : null;
    }

    private function logError($rowNum, $message, $isCritical = true)
    {
        $errorMessage = "Строка " . ($rowNum + 1) . ": " . $message;
        
        $this->errors[] = $errorMessage;
        
        if ($isCritical) {
            $this->skipped++;
        }
        
        Yii::warning($errorMessage, __METHOD__);
    }
    
    /**
     * Парсинг отделов из строки
     * @param string $branchesString Строка с отделами через пробел
     * @return array Массив ID отделов
     */
    private function parseBranches($branchesString)
    {
        $branchesString = trim($branchesString);
        if (empty($branchesString)) {
            return [];
        }

        $branchNames = preg_split('/\s+/', $branchesString);
        
        $branchIds = [];
        foreach ($branchNames as $branchName) {

            $branchId = \common\components\dictionaries\base\BranchDictionary::getByName($branchName);
            
            if ($branchId > 0) {
                $branchIds[] = $branchId;
            } else {
                Yii::warning("Неизвестный отдел: {$branchName}", __METHOD__);
            }
        }

        $branchIds = array_unique($branchIds);
        
        return $branchIds;
    }

   private function prepareActData($participantId, $teacher1Id, $teacher2Id, $focusId, $formId, $branchIds)
    {
        $actData = [
            "type" => 0, // Личное участие
            "personalParticipants" => [$participantId],
            "participant" => null,
            "nomination" => $this->nomination,
            "focus" => $focusId,
            "form" => $formId,
            "firstTeacher" => $teacher1Id,
            "secondTeacher" => $teacher2Id, // Может быть null
            "branch" => $branchIds,
            "team" => null,
            "allowRemote" => null,
        ];
        
        Yii::info("Подготовленные данные: " . json_encode([
            'participantId' => $participantId,
            'teacher1Id' => $teacher1Id,
            'teacher2Id' => $teacher2Id,
            'focusId' => $focusId,
            'formId' => $formId,
            'branchIds' => $branchIds,
            'nomination' => $this->nomination,
            'allData' => $actData
        ]), __METHOD__);
        
        return $actData;
    }
    
   private function createActParticipant($actData)
    {
        try {
            if (empty($actData['personalParticipants']) || 
                empty($actData['nomination']) || 
                empty($actData['focus']) || 
                empty($actData['form']) || 
                empty($actData['firstTeacher']) || 
                $actData['type'] === null) {
                
                throw new \Exception("Не все обязательные поля заполнены для создания акта");
            }
            
            if (empty($actData['firstTeacher']) && empty($actData['secondTeacher'])) {
                throw new \Exception("Должен быть указан хотя бы один педагог");
            }

            $acts = [$actData];

            $wasCreated = $this->actParticipantService->addActParticipant($acts, $this->foreignEventId);
            
            if ($wasCreated) {
                return true;
            } else {

                throw new \Exception("Участник уже есть в этом приказе с данной номинацией");
            }
            
        } catch (\Exception $e) {
            Yii::error("Ошибка в createActParticipant: " . $e->getMessage(), __METHOD__);
            throw $e;
        }
    }
    
    /**
     * Формирует результат импорта
     * @return array
     */
    private function getResults()
    {
        $realErrors = 0;
        foreach ($this->errors as $error) {
            if (strpos($error, '!') === false) { 
                $realErrors++;
            }
        }
        return [
            'success' => empty($this->errors) && $this->skipped === 0,
            'processed' => $this->processed,
            'skipped' => $this->skipped,
            'total' => $this->totalRows,
            'errors' => $this->errors,
            'hasErrors' => !empty($this->errors) || $this->skipped > 0,
        ];
    }
}