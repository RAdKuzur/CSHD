<?php

namespace common\components\dictionaries\base;

use common\models\Error;
use common\services\general\errors\ErrorAchieveService;
use common\services\general\errors\ErrorChangeableService;
use common\services\general\errors\ErrorDocumentService;
use common\services\general\errors\ErrorJournalService;
use common\services\general\errors\ErrorMaterialService;

class ErrorDictionary extends BaseDictionary
{
    const MATERIAL_001 = 46;
    const MATERIAL_002 = 47;
    const MATERIAL_003 = 48;
    const MATERIAL_004 = 49;
    const MATERIAL_005 = 50;
    const MATERIAL_006 = 51;
    const MATERIAL_007 = 52;
    const MATERIAL_008 = 53;
    const MATERIAL_009 = 54;
    const MATERIAL_010 = 55;
    const MATERIAL_011 = 56;

    const ACHIEVE_001 = 22;
    const ACHIEVE_002 = 23;
    const ACHIEVE_003 = 24;
    const ACHIEVE_004 = 25;
    const ACHIEVE_005 = 26;
    const ACHIEVE_006 = 27;
    const ACHIEVE_007 = 30;
    const ACHIEVE_008 = 31;
    const ACHIEVE_009 = 32;
    const ACHIEVE_010 = 33;
    const ACHIEVE_011 = 34;
    const ACHIEVE_012 = 35;
    const ACHIEVE_013 = 43;

    const DOCUMENT_001 = 17;
    const DOCUMENT_002 = 18;
    const DOCUMENT_003 = 19;
    const DOCUMENT_004 = 20;
    const DOCUMENT_005 = 37;
    const DOCUMENT_006 = 57;
    const DOCUMENT_007 = 58;
    const DOCUMENT_008 = 59;
    const DOCUMENT_009 = 60;
    const DOCUMENT_010 = 61;
    const DOCUMENT_011 = 62;
    const DOCUMENT_012 = 63;
    const DOCUMENT_013 = 64;

    const JOURNAL_001 = 1;
    const JOURNAL_002 = 2;
    const JOURNAL_003 = 3;
    const JOURNAL_004 = 4;
    const JOURNAL_005 = 5;
    const JOURNAL_006 = 6;
    const JOURNAL_008 = 8;
    const JOURNAL_009 = 9;
    const JOURNAL_010 = 10;
    const JOURNAL_011 = 11;
    const JOURNAL_012 = 12;
    const JOURNAL_013 = 13;
    const JOURNAL_014 = 14;
    const JOURNAL_015 = 15;
    const JOURNAL_016 = 16;
    const JOURNAL_017 = 21;
    const JOURNAL_018 = 28;
    const JOURNAL_019 = 29;
    const JOURNAL_020 = 36;
    const JOURNAL_021 = 38;
    const JOURNAL_022 = 39;
    const JOURNAL_023 = 40;
    const JOURNAL_024 = 41;
    const JOURNAL_025 = 42;
    const JOURNAL_026 = 44;
    const JOURNAL_027 = 45;

    const MATERIAL_ERRORS = [
        self::MATERIAL_001, self::MATERIAL_002, self::MATERIAL_003,
        self::MATERIAL_004, self::MATERIAL_005, self::MATERIAL_006,
        self::MATERIAL_007, self::MATERIAL_008, self::MATERIAL_009,
        self::MATERIAL_010, self::MATERIAL_011,
    ];

    const DOCUMENT_ERRORS = [
        self::DOCUMENT_001, self::DOCUMENT_002, self::DOCUMENT_003,
        self::DOCUMENT_004, self::DOCUMENT_005, self::DOCUMENT_006,
        self::DOCUMENT_007, self::DOCUMENT_008, self::DOCUMENT_009,
        self::DOCUMENT_010, self::DOCUMENT_011, self::DOCUMENT_012,
        self::DOCUMENT_013
    ];

    const ACHIEVE_ERRORS = [
        self::ACHIEVE_001, self::ACHIEVE_002, self::ACHIEVE_003,
        self::ACHIEVE_004, self::ACHIEVE_005, self::ACHIEVE_006,
        self::ACHIEVE_007, self::ACHIEVE_008, self::ACHIEVE_009,
        self::ACHIEVE_010, self::ACHIEVE_011, self::ACHIEVE_012,
        self::ACHIEVE_013,
    ];

    const JOURNAL_ERRORS = [
        self::JOURNAL_001, self::JOURNAL_002, self::JOURNAL_003,
        self::JOURNAL_004, self::JOURNAL_005, self::JOURNAL_006,
        self::JOURNAL_008, self::JOURNAL_009, self::JOURNAL_010,
        self::JOURNAL_011, self::JOURNAL_012, self::JOURNAL_013,
        self::JOURNAL_014, self::JOURNAL_015, self::JOURNAL_016,
        self::JOURNAL_017, self::JOURNAL_018, self::JOURNAL_019,
        self::JOURNAL_020, self::JOURNAL_021, self::JOURNAL_022,
        self::JOURNAL_023, self::JOURNAL_024, self::JOURNAL_025,
        self::JOURNAL_026, self::JOURNAL_027,
    ];

    private ErrorMaterialService $materialService;
    private ErrorAchieveService $achieveService;
    private ErrorDocumentService $documentService;
    private ErrorJournalService $journalService;
    private ErrorChangeableService $changeableService;

    public function __construct(
        ErrorMaterialService $materialService,
        ErrorAchieveService $achieveService,
        ErrorDocumentService $documentService,
        ErrorJournalService $journalService,
        ErrorChangeableService $changeableService
    )
    {
        parent::__construct();
        $this->materialService = $materialService;
        $this->achieveService = $achieveService;
        $this->documentService = $documentService;
        $this->journalService = $journalService;
        $this->changeableService = $changeableService;

        $this->list = [
            self::MATERIAL_001 => new Error(
                'МЦ001', 'В договоре отсутствуют сведения о категориях материальных ценностей',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_001'],
                [$this->materialService, 'fixMaterial_001'],
            ),
            self::MATERIAL_002 => new Error(
                'МЦ002', 'В договоре отсутствуют ключевые слова',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_002'],
                [$this->materialService, 'fixMaterial_002'],
            ),
            self::MATERIAL_003 => new Error(
                'МЦ003', 'В договоре отсутствует скан документа',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_003'],
                [$this->materialService, 'fixMaterial_003'],
            ),
            self::MATERIAL_004 => new Error(
                'МЦ004', 'В договоре отсутствует скан документа',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_004'],
                [$this->materialService, 'fixMaterial_004'],
            ),
            self::MATERIAL_005 => new Error(
                'МЦ005', 'В документе о поступлении материальных ценностей отсутствует договор',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_005'],
                [$this->materialService, 'fixMaterial_005'],
            ),
            self::MATERIAL_006 => new Error(
                'МЦ006', 'В документе о поступлении материальных ценностей отсутствуют записи',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_006'],
                [$this->materialService, 'fixMaterial_006'],
            ),
            self::MATERIAL_007 => new Error(
                'МЦ007', 'У материальной ценности отсутствует местоположение (контейнер)',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_007'],
                [$this->materialService, 'fixMaterial_007'],
            ),
            self::MATERIAL_008 => new Error(
                'МЦ008', 'Нарушена иерархия контейнеров',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_008'],
                [$this->materialService, 'fixMaterial_008'],
            ),
            self::MATERIAL_009 => new Error(
                'МЦ009', 'Контейнер не содержит материальных ценностей',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_009'],
                [$this->materialService, 'fixMaterial_009'],
            ),
            self::MATERIAL_010 => new Error(
                'МЦ010', 'У материальной ценности отсутствует инвентарный номер',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_010'],
                [$this->materialService, 'fixMaterial_010'],
            ),
            self::MATERIAL_011 => new Error(
                'МЦ011', 'У материальной ценности отсутствует МОЛ',
                Error::TYPE_BASE,
                [$this->materialService, 'makeMaterial_011'],
                [$this->materialService, 'fixMaterial_011'],
            ),

            self::ACHIEVE_001 => new Error(
                'УД001', 'Дата окончания раньше даты начала мероприятия',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_001'],
                [$this->achieveService, 'fixAchieve_001'],
            ),
            self::ACHIEVE_002 => new Error(
                'УД002', 'Не указан город проведения',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_002'],
                [$this->achieveService, 'fixAchieve_002'],
            ),
            self::ACHIEVE_003 => new Error(
                'УД003', 'Не заполнены участники мероприятия',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_003'],
                [$this->achieveService, 'fixAchieve_003'],
            ),
            self::ACHIEVE_004 => new Error(
                'УД004', 'Учащийся не проходил обучение в указанном отделе учета',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_004'],
                [$this->achieveService, 'fixAchieve_004'],
            ),
            self::ACHIEVE_005 => new Error(
                'УД005', 'Не заполнены достижения в карточке учёта',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_005'],
                [$this->achieveService, 'fixAchieve_005'],
            ),
            self::ACHIEVE_006 => new Error(
                'УД006', 'Отсутствуют прикрепленные документы о достижениях участников',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_006'],
                [$this->achieveService, 'fixAchieve_006'],
            ),
            self::ACHIEVE_007 => new Error(
                'УД007', 'В учете достижения мероприятий не указан организатор мероприятия',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_007'],
                [$this->achieveService, 'fixAchieve_007'],
            ),
            self::ACHIEVE_008 => new Error(
                'УД008', 'В мероприятии не указан формат проведения мероприятия',
                Error::TYPE_CRITICAL,
                [$this->achieveService, 'makeAchieve_008'],
                [$this->achieveService, 'fixAchieve_008'],
            ),
            self::ACHIEVE_009 => new Error(
                'УД009', 'В мероприятии отсутствует отдел',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_009'],
                [$this->achieveService, 'fixAchieve_009'],
            ),
            self::ACHIEVE_010 => new Error(
                'УД010', 'В мероприятии отсутствует приказ',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_010'],
                [$this->achieveService, 'fixAchieve_010'],
            ),
            self::ACHIEVE_011 => new Error(
                'УД011', 'В мероприятии отсутствуют фотоматериалы',
                Error::TYPE_BASE,
                [$this->achieveService, 'makeAchieve_011'],
                [$this->achieveService, 'fixAchieve_011'],
            ),
            self::ACHIEVE_012 => new Error(
                'УД012', 'В мероприятии отсутствуют ключевые слова',
                Error::TYPE_CRITICAL,
                [$this->achieveService, 'makeAchieve_012'],
                [$this->achieveService, 'fixAchieve_012'],
            ),
            self::ACHIEVE_013 => new Error(
                'УД013', 'У участника(-ов) мероприятия отсутствует отдел учёта достижения',
                Error::TYPE_CRITICAL,
                [$this->achieveService, 'makeAchieve_013'],
                [$this->achieveService, 'fixAchieve_013'],
            ),

            self::DOCUMENT_001 => new Error(
                'ЭД001', 'В приказе отсутствует скан документа',
                Error::TYPE_CHANGEABLE,
                [$this->documentService, 'makeDocument_001'],
                [$this->documentService, 'fixDocument_001'],
                [$this->changeableService, 'changeDocument_001'],
            ),
            self::DOCUMENT_002 => new Error(
                'ЭД002', 'В приказе отсутствует редактируемый файл',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_002'],
                [$this->documentService, 'fixDocument_002'],
            ),
            self::DOCUMENT_003 => new Error(
                'ЭД003', 'В приказе отсутствуют ключевые слова',
                Error::TYPE_CRITICAL,
                [$this->documentService, 'makeDocument_003'],
                [$this->documentService, 'fixDocument_003'],
            ),
            self::DOCUMENT_004 => new Error(
                'ЭД004', 'В образовательном приказе отсутствуют учебные группы',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_004'],
                [$this->documentService, 'fixDocument_004'],
            ),
            self::DOCUMENT_005 => new Error(
                'ЭД005', 'В образовательном приказе отсутствуют обучающиеся',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_005'],
                [$this->documentService, 'fixDocument_005'],
            ),
            self::DOCUMENT_006 => new Error(
                'ЭД006', 'В приказе об участии отсутствует информация для создания карточки учёта достижений',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_006'],
                [$this->documentService, 'fixDocument_006'],
            ),
            self::DOCUMENT_007 => new Error(
                'ЭД007', 'В приказе об участии отсутствует дополнительная информация для генерации приказа',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_007'],
                [$this->documentService, 'fixDocument_007'],
            ),
            self::DOCUMENT_008 => new Error(
                'ЭД008', 'В исходящем письме отсутствует скан документа',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_008'],
                [$this->documentService, 'fixDocument_008'],
            ),
            self::DOCUMENT_009 => new Error(
                'ЭД009', 'В исходящем письме отсутствует редактируемый файл',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_009'],
                [$this->documentService, 'fixDocument_009'],
            ),
            self::DOCUMENT_010 => new Error(
                'ЭД010', 'В исходящем письме отсутствуют ключевые слова',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_010'],
                [$this->documentService, 'fixDocument_010'],
            ),
            self::DOCUMENT_011 => new Error(
                'ЭД011', 'Во входящем письме отсутствует скан документа',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_011'],
                [$this->documentService, 'fixDocument_011'],
            ),
            self::DOCUMENT_012 => new Error(
                'ЭД012', 'Во входящем письме отсутствуют ключевые слова',
                Error::TYPE_BASE,
                [$this->documentService, 'makeDocument_012'],
                [$this->documentService, 'fixDocument_012'],
            ),
            self::DOCUMENT_013 => new Error(
                'ЭД013', 'На входящее письмо ожидается ответ',
                Error::TYPE_CHANGEABLE,
                [$this->documentService, 'makeDocument_013'],
                [$this->documentService, 'fixDocument_013'],
                [$this->changeableService, 'changeDocument_013'],
            ),

            self::JOURNAL_001 => new Error(
                'ЭЖ001', 'Не указан педагог в карточке группы',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_001'],
                [$this->journalService, 'fixJournal_001'],
                [$this->changeableService, 'changeJournal_001'],
            ),
            self::JOURNAL_002 => new Error(
                'ЭЖ002', 'Не заполнено поле «Приказы» в карточке группы',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_002'],
                [$this->journalService, 'fixJournal_002'],
                [$this->changeableService, 'changeJournal_002'],
            ),
            self::JOURNAL_003 => new Error(
                'ЭЖ003', 'Не заполнено поле «Фотоматериалы» в карточке группы',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_003'],
                [$this->journalService, 'fixJournal_003'],
                [$this->changeableService, 'changeJournal_003'],
            ),
            self::JOURNAL_004 => new Error(
                'ЭЖ004', 'Не заполнено поле «Презентационные материалы» в карточке группы',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_004'],
                [$this->journalService, 'fixJournal_004'],
                [$this->changeableService, 'changeJournal_004'],
            ),
            self::JOURNAL_005 => new Error(
                'ЭЖ005', 'Не заполнено поле «Рабочие материалы» в карточке группы',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_005'],
                [$this->journalService, 'fixJournal_005'],
                [$this->changeableService, 'changeJournal_005'],
            ),
            self::JOURNAL_006 => new Error(
                'ЭЖ006', 'Объем расписания не равен объему программы в карточке группы',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_006'],
                [$this->journalService, 'fixJournal_006'],
                [$this->changeableService, 'changeJournal_006'],
            ),
            self::JOURNAL_008 => new Error(
                'ЭЖ008', 'Нет сведений о сертификатах об обучении в карточке группы',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_008'],
                [$this->journalService, 'fixJournal_008'],
            ),
            self::JOURNAL_009 => new Error(
                'ЭЖ009', 'В журнале нет сведений о явке учащихся',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_009'],
                [$this->journalService, 'fixJournal_009'],
                [$this->changeableService, 'changeJournal_009'],
            ),
            self::JOURNAL_010 => new Error(
                'ЭЖ010', 'В образовательной программе не заполнено тематическое направление',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_010'],
                [$this->journalService, 'fixJournal_010'],
            ),
            self::JOURNAL_011 => new Error(
                'ЭЖ011', 'В учебно-тематическом плане образовательной программе не указана форма контроля',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_001'],
                [$this->journalService, 'fixJournal_001'],
            ),
            self::JOURNAL_012 => new Error(
                'ЭЖ012', 'В образовательной программе количество академических часов не совпадает с учебно-тематическим планом',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_012'],
                [$this->journalService, 'fixJournal_012'],
                [$this->changeableService, 'changeJournal_012'],
            ),
            self::JOURNAL_013 => new Error(
                'ЭЖ013', 'В образовательной программе не указаны составители',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_013'],
                [$this->journalService, 'fixJournal_013'],
            ),
            self::JOURNAL_014 => new Error(
                'ЭЖ014', 'В расписании учебной группы указано некорректное помещение (не предназначенное для учебных целей)',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_014'],
                [$this->journalService, 'fixJournal_014'],
            ),
            self::JOURNAL_015 => new Error(
                'ЭЖ015', 'В журнале группы не заполнены темы занятий и/или педагог',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_015'],
                [$this->journalService, 'fixJournal_015'],
            ),
            self::JOURNAL_016 => new Error(
                'ЭЖ016', 'Дата окончания занятий группы не совпадает с датой последнего занятия в расписании',
                Error::TYPE_CRITICAL,
                [$this->journalService, 'makeJournal_016'],
                [$this->journalService, 'fixJournal_016'],
            ),
            self::JOURNAL_017 => new Error(
                'ЭЖ017', 'Учебная группа должна находиться в архиве',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_017'],
                [$this->journalService, 'fixJournal_017'],
                [$this->changeableService, 'changeJournal_017'],
            ),
            self::JOURNAL_018 => new Error(
                'ЭЖ018', 'В образовательной программе не указан отдел реализации',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_018'],
                [$this->journalService, 'fixJournal_018'],
            ),
            self::JOURNAL_019 => new Error(
                'ЭЖ019', 'В образовательной программе не указана дата педагогического совета',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_019'],
                [$this->journalService, 'fixJournal_019'],
            ),
            self::JOURNAL_020 => new Error(
                'ЭЖ020', 'В учебной группе есть дети, которые не фигурируют в приказах о зачислении и/или отчислении',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_020'],
                [$this->journalService, 'fixJournal_020'],
            ),
            self::JOURNAL_021 => new Error(
                'ЭЖ021', 'В учебной группе отсутствует дата защиты',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_021'],
                [$this->journalService, 'fixJournal_021'],
                [$this->changeableService, 'changeJournal_021'],
            ),
            self::JOURNAL_022 => new Error(
                'ЭЖ022', 'В учебной группе отсутствует тема проекта',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_022'],
                [$this->journalService, 'fixJournal_022'],
                [$this->changeableService, 'changeJournal_022'],
            ),
            self::JOURNAL_023 => new Error(
                'ЭЖ023', 'В учебной группе отсутствует эксперт предстоящей защиты',
                Error::TYPE_CHANGEABLE,
                [$this->journalService, 'makeJournal_023'],
                [$this->journalService, 'fixJournal_023'],
                [$this->changeableService, 'changeJournal_023'],
            ),
            self::JOURNAL_024 => new Error(
                'ЭЖ024', 'Дата защиты раньше даты окончания занятий',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_024'],
                [$this->journalService, 'fixJournal_024'],
            ),
            self::JOURNAL_025 => new Error(
                'ЭЖ025', 'В журнале отсутствуют сведения о теме проекта',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_025'],
                [$this->journalService, 'fixJournal_025'],
            ),
            self::JOURNAL_026 => new Error(
                'ЭЖ026', 'В образовательной программе отсутствует документ программы',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_026'],
                [$this->journalService, 'fixJournal_026'],
            ),
            self::JOURNAL_027 => new Error(
                'ЭЖ027', 'В образовательной программе отсутствует редактируемый документ программы',
                Error::TYPE_BASE,
                [$this->journalService, 'makeJournal_027'],
                [$this->journalService, 'fixJournal_027'],
            ),
        ];
    }

    public function customSort()
    {
        return [

        ];
    }
}