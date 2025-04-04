<?php

namespace console\controllers;

use common\repositories\dictionaries\CompanyRepository;
use common\repositories\rubac\PermissionFunctionRepository;
use common\repositories\rubac\PermissionTemplateFunctionRepository;
use common\repositories\rubac\PermissionTemplateRepository;
use frontend\models\work\rubac\PermissionFunctionWork;
use frontend\models\work\rubac\PermissionTemplateFunctionWork;
use frontend\models\work\rubac\PermissionTemplateWork;
use frontend\models\work\rubac\UserPermissionFunctionWork;
use Yii;
use yii\console\Controller;

class FillController extends Controller
{
    private PermissionTemplateRepository $templateRepository;
    private PermissionFunctionRepository $functionRepository;
    private PermissionTemplateFunctionRepository $templateFunctionRepository;
    private CompanyRepository $companyRepository;

    private $tIds = [];
    private $fIds = [];

    public function __construct(
                                             $id,
                                             $module,
        PermissionTemplateRepository         $templateRepository,
        PermissionFunctionRepository         $functionRepository,
        PermissionTemplateFunctionRepository $templateFunctionRepository,
        CompanyRepository                    $companyRepository,
                                             $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->templateRepository = $templateRepository;
        $this->functionRepository = $functionRepository;
        $this->templateFunctionRepository = $templateFunctionRepository;
        $this->companyRepository = $companyRepository;
    }

    public function actionInit()
    {
        $this->companyRepository->save(
            $this->companyRepository->fastCreateWithId(
                Yii::$app->params['mainCompanyId'],
                'ГАОУ АО ДО "Региональный школьный технопарк',
                'РШТ',
                0)
        );

        $this->actionInitTemplates();
    }

    public function actionDropPermissions()
    {
        UserPermissionFunctionWork::deleteAll();
        PermissionTemplateFunctionWork::deleteAll();
        PermissionFunctionWork::deleteAll();
        PermissionTemplateWork::deleteAll();
    }

    public function actionInitTemplates()
    {
        $this->actionDropPermissions();

        $this->createTemplates();
        $this->createFunctions();

        $this->createAdminRole();
    }

    private function createTemplates()
    {
        $tIds[1] = $this->templateRepository->save(PermissionTemplateWork::fill('teacher', 1));
        $tIds[2] = $this->templateRepository->save(PermissionTemplateWork::fill('study_info', 2));
        $tIds[3] = $this->templateRepository->save(PermissionTemplateWork::fill('event_info', 3));
        $tIds[4] = $this->templateRepository->save(PermissionTemplateWork::fill('doc_info', 4));
        $tIds[5] = $this->templateRepository->save(PermissionTemplateWork::fill('material_info', 5));
        $tIds[6] = $this->templateRepository->save(PermissionTemplateWork::fill('branch_controller', 6));
        $tIds[7] = $this->templateRepository->save(PermissionTemplateWork::fill('super_controller', 7));
        $tIds[8] = $this->templateRepository->save(PermissionTemplateWork::fill('admin', 8));
    }

    private function createFunctions()
    {
        $fIds[1] = $this->functionRepository->save(PermissionFunctionWork::fill('Добавление новых учебных групп', 'add_group', 1));
        $fIds[2] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр своих учебных групп', 'view_self_groups', PermissionFunctionWork::PERMISSION_THEIR_GROUPS_ID));
        $fIds[3] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учебных групп своего отдела', 'view_branch_groups', PermissionFunctionWork::PERMISSION_BRANCH_GROUPS_ID));
        $fIds[4] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр всех учебных групп', 'view_all_groups', PermissionFunctionWork::PERMISSION_ALL_GROUPS_ID));
        $fIds[5] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование своих учебных групп', 'edit_self_groups', 5));
        $fIds[6] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учебных групп своего отдела', 'edit_branch_groups', 6));
        $fIds[7] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование всех учебных групп', 'edit_all_groups', 7));
        $fIds[8] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление учебных групп своего отдела', 'delete_branch_groups', 8));
        $fIds[9] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление всех учебных групп', 'delete_all_groups', 9));
        $fIds[10] = $this->functionRepository->save(PermissionFunctionWork::fill('Архивирование учебных групп своего отдела', 'archive_branch_groups', 10));
        $fIds[11] = $this->functionRepository->save(PermissionFunctionWork::fill('Архивирование всех учебных групп', 'archive_all_groups', 11));
        $fIds[12] = $this->functionRepository->save(PermissionFunctionWork::fill('Прощение ошибок в образовательной деятельности', 'forgive_study_errors', 12));
        $fIds[13] = $this->functionRepository->save(PermissionFunctionWork::fill('Прощение ошибок в основной деятельности', 'forgive_base_errors', 13));
        $fIds[14] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление участников деятельности', 'delete_participants', 14));
        $fIds[15] = $this->functionRepository->save(PermissionFunctionWork::fill('Слияние участников деятельности', 'merge_participants', 15));
        $fIds[16] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр образовательных программ', 'view_training_programs', 16));
        $fIds[17] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование образовательных программ', 'edit_training_programs', 17));
        $fIds[18] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр приказов по мероприятиям', 'view_event_orders', 18));
        $fIds[19] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование приказов по мероприятиям', 'edit_event_orders', 19));
        $fIds[20] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр приказов по образовательной деятельности', 'view_study_orders', 20));
        $fIds[21] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование приказов по образовательной деятельности', 'edit_study_orders', 21));
        $fIds[22] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр приказов по основной деятельности', 'view_base_orders', 22));
        $fIds[23] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование приказов по основной деятельности', 'edit_base_orders', 23));
        $fIds[24] = $this->functionRepository->save(PermissionFunctionWork::fill('Генерирование отчетов по запросу', 'gen_report_query', 24));
        $fIds[25] = $this->functionRepository->save(PermissionFunctionWork::fill('Генерирование отчетов по формам', 'gen_report_forms', 25));
        $fIds[26] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр входящей документации', 'view_doc_in', 26));
        $fIds[27] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование входящей документации', 'edit_doc_in', 27));
        $fIds[28] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр исходящей документации', 'view_doc_out', 28));
        $fIds[29] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование исходящей документации', 'edit_doc_out', 29));
        $fIds[30] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр положений о мероприятиях', 'view_event_regulations', 30));
        $fIds[31] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование положений о мероприятиях', 'edit_event_regulations', 31));
        $fIds[32] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр положений, инструкций и правил', 'view_base_regulations', 32));
        $fIds[33] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование положений, инструкций и правил', 'edit_base_regulations', 33));
        $fIds[34] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр мероприятий', 'view_events', 34));
        $fIds[35] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование мероприятий', 'edit_events', 35));
        $fIds[36] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учета достижений в мероприятиях', 'view_foreign_events', 36));
        $fIds[37] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учета достижений в мероприятиях', 'edit_foreign_events', 37));
        $fIds[38] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр учета ответственности работников', 'view_local_resp', 38));
        $fIds[39] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование учета ответственности работников', 'edit_local_resp', 39));
        $fIds[40] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр пользователей', 'view_users', 40));
        $fIds[41] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование пользователей', 'edit_users', 41));
        $fIds[42] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование правил', 'edit_permissions', 42));
        $fIds[43] = $this->functionRepository->save(PermissionFunctionWork::fill('Создание сертификатов', 'create_certificates', 43));
        $fIds[44] = $this->functionRepository->save(PermissionFunctionWork::fill('Удаление сертификатов', 'delete_certificates', 44));
        $fIds[45] = $this->functionRepository->save(PermissionFunctionWork::fill('Доступ к основным административным функциям', 'allow_base_admin', 45));
        $fIds[46] = $this->functionRepository->save(PermissionFunctionWork::fill('Доступ к дополнительным административным функциям', 'allow_extended_admin', 46));
        $fIds[47] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника шаблонов сертификатов', 'view_certificate_template', 47));
        $fIds[48] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника шаблонов сертификатов', 'edit_certificate_template', 48));
        $fIds[49] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр мат. объектов и их объединений', 'view_material_obj', 49));
        $fIds[50] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование мат. объектов и их объединений', 'edit_material_obj', 50));
        $fIds[51] = $this->functionRepository->save(PermissionFunctionWork::fill('Внутреннее перемещение мат. объектов по МОЛ', 'move_material_obj', 51));
        $fIds[52] = $this->functionRepository->save(PermissionFunctionWork::fill('Просмотр справочника организаций', 'view_dictionaries', 52));
        $fIds[53] = $this->functionRepository->save(PermissionFunctionWork::fill('Редактирование справочника организаций', 'edit_dictionaries', 53));
        $fIds[54] = $this->functionRepository->save(PermissionFunctionWork::fill('Подтверждение тем проектов', 'confirm_themes', 54));
        $fIds[55] = $this->functionRepository->save(PermissionFunctionWork::fill('Получение ошибок учета достижений', 'get_achieve_errors', 55));
        $fIds[56] = $this->functionRepository->save(PermissionFunctionWork::fill('Получение ошибок электронного документооборота', 'get_journal_errors', 56));
        $fIds[57] = $this->functionRepository->save(PermissionFunctionWork::fill('Получение ошибок электронного журнала', 'get_document_errors', 57));
        $fIds[58] = $this->functionRepository->save(PermissionFunctionWork::fill('Получение ошибок материальных ценностей', 'get_material_errors', 58));
        $fIds[59] = $this->functionRepository->save(PermissionFunctionWork::fill('Получение ошибок своего отдела', 'get_branch_errors', 59));
        $fIds[60] = $this->functionRepository->save(PermissionFunctionWork::fill('Получение ошибок всех отделов', 'get_all_errors', 60));
    }

    private function createAdminRole()
    {
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 1));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 2));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 3));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 4));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 5));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 6));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 7));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 8));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 9));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 10));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 11));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 12));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 13));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 14));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 15));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 16));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 17));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 18));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 19));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 20));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 21));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 22));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 23));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 24));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 25));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 26));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 27));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 28));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 29));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 30));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 31));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 32));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 33));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 34));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 35));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 36));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 37));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 38));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 39));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 40));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 41));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 42));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 43));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 44));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 45));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 46));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 47));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 48));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 49));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 50));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 51));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 52));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 53));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 54));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 55));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 56));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 57));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 58));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 59));
        $this->templateFunctionRepository->save(PermissionTemplateFunctionWork::fill(8, 60));
    }
}
