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

        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Excel-файл',

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
            'ФИО второго педагога (при необходимости)',
            'Направленность',
            'Форма реализации',
            'Номинация'
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

            $participantId = $this->findParticipant(
                $data['Имя'][$index],
                $data['Фамилия'][$index],
                $data['Отчество'][$index] ?? ''
            );
            
            if (!$participantId) {
                $this->logError($index, 
                    "Участник не найден: {$data['Фамилия'][$index]} {$data['Имя'][$index]} " . 
                    ($data['Отчество'][$index] ?? '')
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
            $teacher2FullName = trim($data['ФИО второго педагога (при необходимости)'][$index] ?? '');
            
            if (!empty($teacher2FullName)) {
                $teacher2Id = $this->findTeacher($teacher2FullName);

                if (!$teacher2Id) {
                    Yii::info("Второй педагог не найден, но это не критично: {$teacher2FullName}", __METHOD__);
                }
            }
        
            $branchIds = $this->parseBranches($data['Отделы'][$index] ?? '');

            
            if (empty($branchIds)) {
                $this->logError($index, "Не указаны или неверно указаны отделы: " . ($data['Отделы'][$index] ?? 'пусто'));
                return;
            }

            $focusId = null; 
            $focusName = trim($data['Направленность'][$index] ?? '');
            if (!empty($focusName)) {
                $focusDict = new \common\components\dictionaries\base\FocusDictionary();
                $focusId = $focusDict->getIdByName($focusName);
                if (!$focusId) {
                    Yii::info("Направленность не найдена в словаре: {$focusName}", __METHOD__);
                }
            }
            
            $formId = null; 
            $formName = trim($data['Форма реализации'][$index] ?? '');
            if (!empty($formName)) {
                $formDict = new \common\components\dictionaries\base\EventWayDictionary();
                $formId = $formDict->getIdByName($formName);
                if (!$formId) {
                    Yii::info("Форма реализации не найдена в словаре: {$formName}", __METHOD__);
                }
            }
            
            $nomination = trim($data['Номинация'][$index] ?? '');
            $nomination = $nomination === '' ? null : $nomination;
            
            $actData = $this->prepareActData(
                $participantId,
                $teacher1Id,
                $teacher2Id,
                $focusId,
                $formId,
                $branchIds,
                $nomination 
            );

            $this->createActParticipant($actData);
        
            $this->processed++;
            
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

        ];

        foreach ($requiredFields as $fieldName => $value) {
            if (empty(trim($value))) {
                $this->logError($index, "Не заполнено обязательное поле: {$fieldName}");
                return false;
            }
        }
        
        return true;
    }
    
     private function findParticipant($firstname, $surname, $patronymic = '')
    {
        if (empty(trim($patronymic))) {
            $participant = $this->foreignEventParticipantsRepository->findByFullName(
                $firstname,
                $surname,
                ''
            );

            if (!$participant) {

            }
        } else {
            $participant = $this->foreignEventParticipantsRepository->findByFullName(
                $firstname,
                $surname,
                $patronymic
            );
        }
        
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
        
        $allPossibleBranches = [
            "Мобильный Кванториум",
            "Центр одаренных детей",
            "Технопарк",
            "Кванториум", 
            "ЦДНТТ",
            "Планетарий",
            "Администрация"
        ];
        
        $branchIds = [];
        
        $positions = [];
        
        foreach ($allPossibleBranches as $branchName) {
            $lastPos = 0;
            
            $branchDict = new \common\components\dictionaries\base\BranchDictionary();
            
            while (($pos = strpos($branchesString, $branchName, $lastPos)) !== false) {
                $positions[] = [
                    'position' => $pos,
                    'length' => strlen($branchName),
                    'name' => $branchName,
                    'id' => $branchDict->getIdByName($branchName)
                ];
                $lastPos = $pos + strlen($branchName);
            }
        }
        
        usort($positions, function($a, $b) {
            return $a['position'] - $b['position'];
        });
        
        $lastEnd = -1;
        foreach ($positions as $pos) {
            if ($pos['position'] > $lastEnd && $pos['id'] > 0) {
                $branchIds[] = $pos['id'];
                $lastEnd = $pos['position'] + $pos['length'] - 1;
            }
        }
        
        return array_unique($branchIds);
    }

    private function prepareActData($participantId, $teacher1Id, $teacher2Id, $focusId, $formId, $branchIds, $nomination)
    {
        $actData = [
            "type" => 0, 
            "personalParticipants" => [$participantId],
            "participant" => null,
            "nomination" => $nomination,
            "focus" => $focusId,
            "form" => $formId,
            "firstTeacher" => $teacher1Id,
            "secondTeacher" => $teacher2Id,
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
            'nomination' => $nomination,
            'allData' => $actData
        ]), __METHOD__);
        
        return $actData;
    }
    
   private function createActParticipant($actData)
    {
        try {
            if (empty($actData['personalParticipants']) || 

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
                throw new \Exception("Участник уже есть в этом приказе" . 
                    ($actData['nomination'] ? " с номинацией '{$actData['nomination']}'" : ""));
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