<?php

namespace common\components\dictionaries\base;

class ResponsibilityTypeDictionary extends BaseDictionary
{
    const FIRE_SAFETY = 1;
    const ENERGY_CONSUMPTION = 2;
    const FEEDBACK_PLATFORM = 3;
    const CONTROL_DSFROP_ATL = 5;
    const VIDEO_SURVEILLANCE = 6;
    const EMERGENCY_RESPONSE = 7;
    const INSPECTION_PASSPORTIZATION = 9;
    const TECH_CONDITION_SUPPORT = 10;
    const INSPECTION_CATEGORIZATION = 11;
    const EPSU_ADMIN = 12;
    const CONTROL_DSFROP_32 = 13;
    const OCCUPATIONAL_SAFETY = 14;
    const MONITORING_COMPLIANCE = 15;
    const SAFE_OPERATION = 16;
    const HAZARD_IDENTIFICATION = 17;
    const SANITARY_CONDITION = 18;
    const USE_OFFICE_EQUIP = 19;
    const NON_BUDGET_ACTIVITY = 20;
    const SITE_ADMIN = 21;
    const FIREWOOD_SUPPLIER = 22;
    const GAS_SUPPLY = 23;
    const CORRUPTION_PREVENTION = 24;
    const INTEREST_CONFLICT = 25;
    const CONTROL_DSFROP_SHOD = 26;
    const CONTROL_DSFROP_28 = 27;
    const CONTROL_DSFROP_BAIBEK = 28;
    const CONTROL_DSFROP_ZNAMENSK = 29;
    const ANTI_CORRUPTION = 30;
    const SEAL_STAMP_PRODUCTION = 31;
    const SEAL_STAMP_STORAGE = 32;

    public function __construct()
    {
        parent::__construct();
        $this->list = [
            self::FIRE_SAFETY => 'Пожарная безопасность',
            self::ENERGY_CONSUMPTION => 'Заполнение декларации о потреблении энергетических ресурсов за 2020 год',
            self::FEEDBACK_PLATFORM => 'Работа в Платформе обратной связи (ПОС)',
            self::CONTROL_DSFROP_ATL => 'Контроль исполнения ДСФРОП с ГБОУ АО "АТЛ"',
            self::VIDEO_SURVEILLANCE => 'Доступ к системе видеоконтроля и видеонаблюдения',
            self::EMERGENCY_RESPONSE => 'Оперативное реагирование на срабатывание программного-аппаратного комплекса системы "Стрелец-Мониторинг"',
            self::INSPECTION_PASSPORTIZATION => 'Комиссия по организации обследования и паспортизации объекта и предоставляемых на нем услуг в сфере образования',
            self::TECH_CONDITION_SUPPORT => 'Техническое состояние вспомогательных средств для инвалидов и других маломобильных групп населения',
            self::INSPECTION_CATEGORIZATION => 'Комиссия по обследованию и категорированию объектов',
            self::EPSU_ADMIN => 'Администратор ЭПСУ',
            self::CONTROL_DSFROP_32 => 'Контроль исполнения ДСФРОП с МБОУ г. Астрахани "СОШ №32"',
            self::OCCUPATIONAL_SAFETY => 'Состояние охраны труда',
            self::MONITORING_COMPLIANCE => 'Обеспечение контроля за соблюдением требований охраны труда и осуществление контроля за выполнением мероприятий по охране труда',
            self::SAFE_OPERATION => 'Безопасная эксплуатация зданий, безопасные организационно-технические мероприятия обслуживающего персонала',
            self::HAZARD_IDENTIFICATION => 'Комиссия по идентификации опасностей о оценке профессиональных рисков',
            self::SANITARY_CONDITION => 'Санитарно-гигиеническое состояние учебных, административных и вспомогательных помещений',
            self::USE_OFFICE_EQUIP => 'Безопасная эксплуатация офисной техники, компьютерного и периферийного оборудования',
            self::NON_BUDGET_ACTIVITY => 'Ведение внебюджетной деятельности',
            self::SITE_ADMIN => 'Администратор официального сайта',
            self::FIREWOOD_SUPPLIER => 'Истопник',
            self::GAS_SUPPLY => 'Состояние газового хозяйства ',
            self::CORRUPTION_PREVENTION => 'Профилактика коррупционных и иных правонарушений',
            self::INTEREST_CONFLICT => 'Комиссия по урегулированию конфликта интересов',
            self::CONTROL_DSFROP_SHOD => 'Контроль исполнения ДСФРОП с ГБОУ АО "ШОД им. А.П. Гужвина"',
            self::CONTROL_DSFROP_28 => 'Контроль исполнения ДСФРОП с МБОУ г. Астрахани "СОШ №28"',
            self::CONTROL_DSFROP_BAIBEK => 'Контроль исполнения ДСФРОП с МБОУ "Байбекская СОШ им. Абая Кунанбаева"',
            self::CONTROL_DSFROP_ZNAMENSK => 'Контроль исполнения ДСФРОП с МКОУДО ЗАТО Знаменск ЦДТ',
            self::ANTI_CORRUPTION => 'Комиссия по противодействию коррупции',
            self::SEAL_STAMP_PRODUCTION => 'Изготовление и учет печатей и штампов',
            self::SEAL_STAMP_STORAGE => 'Хранение и использование печатей и штампов',
        ];
    }

    public function customSort()
    {
        return [
            $this->list[self::FIRE_SAFETY],
            $this->list[self::ENERGY_CONSUMPTION],
            $this->list[self::FEEDBACK_PLATFORM],
            $this->list[self::CONTROL_DSFROP_ATL],
            $this->list[self::VIDEO_SURVEILLANCE],
            $this->list[self::EMERGENCY_RESPONSE],
            $this->list[self::INSPECTION_PASSPORTIZATION],
            $this->list[self::TECH_CONDITION_SUPPORT],
            $this->list[self::INSPECTION_CATEGORIZATION],
            $this->list[self::EPSU_ADMIN],
            $this->list[self::CONTROL_DSFROP_32],
            $this->list[self::OCCUPATIONAL_SAFETY],
            $this->list[self::MONITORING_COMPLIANCE],
            $this->list[self::SAFE_OPERATION],
            $this->list[self::HAZARD_IDENTIFICATION],
            $this->list[self::SANITARY_CONDITION],
            $this->list[self::USE_OFFICE_EQUIP],
            $this->list[self::NON_BUDGET_ACTIVITY],
            $this->list[self::SITE_ADMIN],
            $this->list[self::FIREWOOD_SUPPLIER],
            $this->list[self::GAS_SUPPLY],
            $this->list[self::CORRUPTION_PREVENTION],
            $this->list[self::INTEREST_CONFLICT],
            $this->list[self::CONTROL_DSFROP_SHOD],
            $this->list[self::CONTROL_DSFROP_28],
            $this->list[self::CONTROL_DSFROP_BAIBEK],
            $this->list[self::CONTROL_DSFROP_ZNAMENSK],
            $this->list[self::ANTI_CORRUPTION],
            $this->list[self::SEAL_STAMP_PRODUCTION],
            $this->list[self::SEAL_STAMP_STORAGE],
        ];
    }
}