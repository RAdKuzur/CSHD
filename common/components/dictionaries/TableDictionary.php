<?php

namespace common\components\dictionaries;

use common\components\dictionaries\base\BaseDictionary;
use common\models\scaffold\ActParticipant;
use common\models\scaffold\ActParticipantBranch;
use common\models\scaffold\Auditorium;
use common\models\scaffold\AuthorProgram;
use common\models\scaffold\BotMessage;
use common\models\scaffold\BranchProgram;
use common\models\scaffold\Certificate;
use common\models\scaffold\CertificateTemplates;
use common\models\scaffold\CharacteristicObject;
use common\models\scaffold\Company;
use common\models\scaffold\Complex;
use common\models\scaffold\DocumentIn;
use common\models\scaffold\DocumentOrder;
use common\models\scaffold\DocumentOut;
use common\models\scaffold\Entry;
use common\models\scaffold\Errors;
use common\models\scaffold\Event;
use common\models\scaffold\EventBranch;
use common\models\scaffold\EventScope;
use common\models\scaffold\Expire;
use common\models\scaffold\Files;
use common\models\scaffold\ForeignEvent;
use common\models\scaffold\ForeignEventParticipants;
use common\models\scaffold\GroupProjectThemes;
use common\models\scaffold\InOutDocuments;
use common\models\scaffold\LegacyResponsible;
use common\models\scaffold\LessonTheme;
use common\models\scaffold\LocalResponsibility;
use common\models\scaffold\ObjectStates;
use common\models\scaffold\OrderPeople;
use common\models\scaffold\OrderTrainingGroupParticipant;
use common\models\scaffold\ParticipantAchievement;
use common\models\scaffold\Patchnotes;
use common\models\scaffold\People;
use common\models\scaffold\PeoplePositionCompanyBranch;
use common\models\scaffold\PeopleStamp;
use common\models\scaffold\PermissionFunction;
use common\models\scaffold\PermissionTemplate;
use common\models\scaffold\PermissionTemplateFunction;
use common\models\scaffold\PermissionToken;
use common\models\scaffold\PersonalDataParticipant;
use common\models\scaffold\Position;
use common\models\scaffold\ProductUnion;
use common\models\scaffold\ProjectTheme;
use common\models\scaffold\Regulation;
use common\models\scaffold\RussianNames;
use common\models\scaffold\SquadParticipant;
use common\models\scaffold\TeacherGroup;
use common\models\scaffold\Team;
use common\models\scaffold\TeamName;
use common\models\scaffold\ThematicPlan;
use common\models\scaffold\TrainingGroup;
use common\models\scaffold\TrainingGroupExpert;
use common\models\scaffold\TrainingGroupLesson;
use common\models\scaffold\TrainingGroupParticipant;
use common\models\scaffold\TrainingProgram;
use common\models\scaffold\Visit;
use common\models\User;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\general\RussianNamesWork;
use frontend\models\work\rubac\UserPermissionFunctionWork;

class TableDictionary extends BaseDictionary
{
    public function __construct()
    {
        parent::__construct();
        $this->list = [
            ActParticipant::tableName() => 'Акты участия',
            ActParticipantBranch::tableName() => 'Акт участия - отдел',
            Auditorium::tableName() => 'Помещения',
            AuthorProgram::tableName() => 'Авторы учебных программ',
            BotMessage::tableName() => 'Сообщения бота',
            BranchProgram::tableName() => 'Отделы реализации учебных программ',
            Certificate::tableName() => 'Сертификаты',
            CertificateTemplates::tableName() => 'Шаблоны сертификатов',
            CharacteristicObject::tableName() => 'Характеристики объектов',
            Company::tableName() => 'Организации',
            Complex::tableName() => 'Комплексы объектов',
            DocumentIn::tableName() => 'Входящая документация',
            DocumentOrder::tableName() => 'Приказы',
            DocumentOut::tableName() => 'Исходящая документация',
            Entry::tableName() => 'Документы о поступлении',
            Errors::tableName() => 'Ошибки',
            Event::tableName() => 'Мероприятия',
            EventBranch::tableName() => 'Отделы-мероприятия',
            EventScope::tableName() => 'Направленности мероприятий',
            Expire::tableName() => 'Ограничения или изменения документов',
            Files::tableName() => 'Файлы',
            ForeignEvent::tableName() => 'Учет достижений в мероприятиях',
            ForeignEventParticipants::tableName() => 'Участники деятельности',
            GroupProjectThemes::tableName() => 'Группа - тема проекта',
            InOutDocuments::tableName() => 'Ответы на документы',
            LegacyResponsible::tableName() => 'История передачи ответственности',
            LessonTheme::tableName() => 'Темы занятий',
            LocalResponsibility::tableName() => 'Ответственность работников',
            ObjectStates::tableName() => 'Состояния объектов',
            OrderPeople::tableName() => 'Ответственные по приказам',
            OrderTrainingGroupParticipant::tableName() => 'Ученики в образовательных приказах',
            ParticipantAchievement::tableName() => 'Достижения участником мероприятий',
            Patchnotes::tableName() => 'Патчноуты',
            People::tableName() => 'Люди',
            PeoplePositionCompanyBranch::tableName() => 'Организации-должности-люди',
            PeopleStamp::tableName() => 'Копии людей',
            PermissionFunction::tableName() => 'Функции Rule-Based Access Model',
            PermissionTemplate::tableName() => 'Шаблоны Rule-Based Access Model',
            PermissionTemplateFunction::tableName() => 'Функции для шаблонов Rule-Based Access Model',
            PermissionToken::tableName() => 'Временные токены доступа для Rule-Based Access Model',
            PersonalDataParticipant::tableName() => 'Ограничения разглашения персональных данных',
            Position::tableName() => 'Должности',
            ProductUnion::tableName() => 'Объединения объектов',
            ProjectTheme::tableName() => 'Темы проектов',
            Regulation::tableName() => 'Положения',
            RussianNames::tableName() => 'Русские имена',
            SquadParticipant::tableName() => 'Участник - акт участия',
            TeacherGroup::tableName() => 'Учитель - учебная группа',
            Team::tableName() => 'Команды на мероприятиях',
            TeamName::tableName() => 'Имя команд',
            ThematicPlan::tableName() => 'Тематический план',
            TrainingGroup::tableName() => 'Учебные группы',
            TrainingGroupExpert::tableName() => 'Учебная группа - эксперты на защите',
            TrainingGroupLesson::tableName() => 'Учебная группа - занятия',
            TrainingGroupParticipant::tableName() => 'Учебная группа - ученики',
            TrainingProgram::tableName() => 'Учебные программы',
            User::tableName() => 'Пользователи',
            UserPermissionFunctionWork::tableName() => 'Пользователи-функции Rule-Based Access Model',
            Visit::tableName() => 'Явки/неявки учеников на занятия'
        ];
    }

    public function customSort()
    {
        return [

        ];
    }
}