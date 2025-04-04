<?php


namespace frontend\models\work\auxiliary;

use common\components\wizards\ExcelWizard;
use common\helpers\CompareHelper;
use common\helpers\DateFormatter;
use common\helpers\files\FilePaths;
use common\helpers\StringFormatter;
use common\repositories\dictionaries\ForeignEventParticipantsRepository;
use common\services\general\files\FileService;
use frontend\events\foreign_event_participants\PersonalDataParticipantAttachEvent;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonInterface;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class LoadParticipants extends Model
{
    public $filename;
    public $file;

    private FileService $fileService;
    private ForeignEventParticipantsRepository $participantRepository;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->fileService = Yii::createObject(FileService::class);
        $this->participantRepository = Yii::createObject(ForeignEventParticipantsRepository::class);
    }

    public function rules()
    {
        return [
            ['filename', 'string'],
            [['file'], 'file', 'extensions' => 'xls, xlsx', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    public function save()
    {
        $newFilename = StringFormatter::createHash(date("Y-m-d H:i:s")) . '.' . $this->file->extension;
        $this->fileService->uploadFile($this->file, $newFilename, ['filepath' => FilePaths::TEMP_FILEPATH . '/']);
        $data = ExcelWizard::getDataFromColumns(
            Yii::$app->basePath . FilePaths::TEMP_FILEPATH . '/' . $newFilename,
            ['Фамилия обучающегося', 'Имя обучающегося', 'Отчество обучающегося', 'Дата рождения (л)', 'Контакт: Рабочий e-mail']
        );

        for ($i = 0; $i < count($data['Фамилия обучающегося']); $i++) {
            $birthdate = $data['Дата рождения (л)'][$i] ?
                DateFormatter::format($data['Дата рождения (л)'][$i], DateFormatter::mdY_slash, DateFormatter::Ymd_dash) :
                PersonInterface::BASE_BIRTHDATE;
            $participant = ForeignEventParticipantsWork::fill(
                $data['Имя обучающегося'][$i],
                $data['Фамилия обучающегося'][$i],
                $birthdate,
                CompareHelper::isEmail($data['Контакт: Рабочий e-mail'][$i]) == CompareHelper::RESULT_CORRECT ? $data['Контакт: Рабочий e-mail'][$i] : '',
                $this->participantRepository->getSexByName($data['Имя обучающегося'][$i]),
                $data['Отчество обучающегося'][$i]
            );
            $this->participantRepository->save($participant);
            $participant->recordEvent(new PersonalDataParticipantAttachEvent($participant->id), get_class($participant));
            $participant->releaseEvents();
        }

        $this->fileService->deleteFile(FilePaths::TEMP_FILEPATH . '/' . $newFilename);
    }
}