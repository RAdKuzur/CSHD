<?php

namespace common\components\access;

use Yii;

class RulesConfig
{
    // основные связи правил с экшнами контроллеров
    private $permissionActionLinks = [
        'add_group' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'create',
                'generate-journal',
                'delete-journal'
            ]
        ],

        'view_self_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
                'download-plan'
            ]
        ],

        'view_branch_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
                'download-plan'
            ]
        ],

        'view_all_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
                'download-plan'
            ]
        ],

        'edit_self_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'base-form',
                'participant-form',
                'schedule-form',
                'pitch-form',
                'group-deletion',
                'delete-file',
                'create-lesson-themes',
                'delete-lesson',
                'update-lesson',
                'sub-auds',
                'delete-theme'
            ]
        ],

        'edit_branch_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'base-form',
                'participant-form',
                'schedule-form',
                'pitch-form',
                'group-deletion',
                'delete-file',
                'create-lesson-themes',
                'delete-lesson',
                'update-lesson',
                'sub-auds',
                'create-protocol',
                'delete-theme'
            ]
        ],

        'edit_all_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'base-form',
                'participant-form',
                'schedule-form',
                'pitch-form',
                'group-deletion',
                'delete-file',
                'create-lesson-themes',
                'delete-lesson',
                'update-lesson',
                'sub-auds',
                'create-protocol',
                'download-journal',
                'delete-theme'
            ]
        ],

        'delete_branch_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'delete'
            ]
        ],

        'delete_all_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'delete'
            ]
        ],

        'archive_branch_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'archive',
                'archive-save',
                'archive-group',
                'unarchive-group'
            ]
        ],

        'archive_all_groups' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'archive',
                'archive-save',
                'archive-group',
                'unarchive-group'
            ]
        ],

        'forgive_study_errors' => [
            \frontend\controllers\educational\TrainingGroupController::class => [
                'amnesty'
            ]
        ],

        'forgive_base_errors' => [

        ],

        'delete_participants' => [
            \frontend\controllers\dictionaries\ForeignEventParticipantsController::class => [
                'delete'
            ]
        ],

        'merge_participants' => [
            \frontend\controllers\dictionaries\ForeignEventParticipantsController::class => [
                'merge-participant',
                'info'
            ]
        ],

        'view_training_programs' => [
            \frontend\controllers\educational\TrainingProgramController::class => [
                'index',
                'view',
                'get-file',
            ]
        ],

        'edit_training_programs' => [
            \frontend\controllers\educational\TrainingProgramController::class => [
                'create',
                'update',
                'delete',
                'update-theme',
                'delete-theme',
                'delete-author',
                'delete-file',
                'relevance',
                'relevance-save',
            ]
        ],

        'view_event_orders' => [
            \frontend\controllers\order\OrderEventController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_event_orders' => [
            \frontend\controllers\order\OrderEventController::class => [
                'create',
                'update',
                'delete-file',
                'act',
                'delete-people',
                'act-delete',
                'generate-order'
            ]
        ],

        'view_study_orders' => [
            \frontend\controllers\order\OrderTrainingController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_study_orders' => [
            \frontend\controllers\order\OrderTrainingController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
                'get-list-by-branch',
                'set-name-order',
                'get-group-by-branch',
                'get-group-participants-by-branch',
                'set-preamble',
                'generate-order'
            ]
        ],

        'view_base_orders' => [
            \frontend\controllers\order\OrderMainController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_base_orders' => [
            \frontend\controllers\order\OrderMainController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
                'delete-document',
                'reserve'
            ]
        ],

        'gen_report_query' => [
            \backend\controllers\report\query\ForeignEventReportController::class => [
                'foreign-event',
                'download-debug-csv'
            ]
        ],

        'gen_report_forms' => [
            \backend\controllers\report\query\ManHoursReportController::class => [
                'man-hours',
                'download-debug-csv'
            ]
        ],

        'view_doc_in' => [
            \frontend\controllers\document\DocumentInController::class => [
                'index',
                'view',
                'get-file',
                'dependency-dropdown',
            ],
        ],

        'edit_doc_in' => [
            \frontend\controllers\document\DocumentInController::class => [
                'create',
                'update',
                'delete',
                'reserve',
                'delete-file',
            ],
        ],

        'view_doc_out' => [
            \frontend\controllers\document\DocumentOutController::class => [
                'index',
                'view',
                'get-file',
                'dependency-dropdown',
            ],
        ],

        'edit_doc_out' => [
            \frontend\controllers\document\DocumentOutController::class => [
                'create',
                'update',
                'delete',
                'reserve',
                'delete-file',
            ],
        ],

        'view_event_regulations' => [
            \frontend\controllers\regulation\RegulationEventController::class => [
                'index',
                'view',
                'get-file',
            ],
        ],

        'edit_event_regulations' => [
            \frontend\controllers\regulation\RegulationEventController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
            ],
        ],

        'view_base_regulations' => [
            \frontend\controllers\regulation\RegulationController::class => [
                'index',
                'view',
                'get-file',
            ],
        ],

        'edit_base_regulations' => [
            \frontend\controllers\regulation\RegulationController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
            ],
        ],

        'view_events' => [
            \frontend\controllers\event\OurEventController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_events' => [
            \frontend\controllers\event\OurEventController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
            ]
        ],

        'view_foreign_events' => [
            \frontend\controllers\event\ForeignEventController::class => [
                'index',
                'view',
                'get-file',
                'get-files'
            ]
        ],

        'edit_foreign_events' => [
            \frontend\controllers\event\ForeignEventController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
                'update-participant',
                'update-achievement',
                'delete-achievement'
            ]
        ],

        'view_local_resp' => [
            \frontend\controllers\responsibility\LocalResponsibilityController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
            ]
        ],

        'edit_local_resp' => [
            \frontend\controllers\responsibility\LocalResponsibilityController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
                'get-auditorium',
            ]
        ],

        'view_users' => [
            \backend\controllers\UserController::class => [
                'index',
                'view'
            ]
        ],

        'edit_users' => [
            \frontend\controllers\user\UserController::class => [
                'create',
                'update',
                'tokens',
                'delete-token',
                'delete'
            ]
        ],

        'edit_permissions' => [

        ],

        'create_certificates' => [
            \frontend\controllers\educational\CertificateController::class => [
                'index',
                'view',
                'download-archive',
                'send-all',
                'send-pdf',
                'get-groups',
                'get-participants'
            ]
        ],

        'delete_certificates' => [
            \frontend\controllers\educational\CertificateController::class => [
                'create',
                'generation-pdf'
            ]
        ],

        'allow_base_admin' => [
            \backend\controllers\UserController::class => [
                'index',
                'view',
                'create',
                'update',
                'tokens',
                'delete-token'
            ]
        ],

        'allow_extended_admin' => [

        ],

        'view_certificate_template' => [
            \backend\controllers\CertificateTemplatesController::class => [
                'index',
                'view',
                'get-image'
            ]
        ],

        'edit_certificate_template' => [
            \backend\controllers\CertificateTemplatesController::class => [
                'create-template',
                'update',
                'delete'
            ]
        ],

        'view_material_obj' => [

        ],

        'edit_material_obj' => [

        ],

        'move_material_obj' => [

        ],

        'view_dictionaries' => [
            \frontend\controllers\dictionaries\AuditoriumController::class => [
                'index',
                'view',
                'get-file',
                'get-files',
            ],
            \frontend\controllers\dictionaries\CompanyController::class => [
                'index',
                'view',
            ],
            \frontend\controllers\dictionaries\ForeignEventParticipantsController::class => [
                'index',
                'view',
            ],
            \frontend\controllers\dictionaries\PeopleController::class => [
                'index',
                'view',
            ],
            \frontend\controllers\dictionaries\PositionController::class => [
                'index',
                'view',
            ],
        ],

        'edit_dictionaries' => [
            \frontend\controllers\dictionaries\AuditoriumController::class => [
                'create',
                'update',
                'delete',
                'delete-file',
            ],
            \frontend\controllers\dictionaries\CompanyController::class => [
                'create',
                'update',
                'delete'
            ],
            \frontend\controllers\dictionaries\ForeignEventParticipantsController::class => [
                'create',
                'update',
                'delete',
                'file-load',
                'check-correct',
            ],
            \frontend\controllers\dictionaries\PeopleController::class => [
                'create',
                'update',
                'delete',
                'delete-position'
            ],
            \frontend\controllers\dictionaries\PositionController::class => [
                'create',
                'update',
                'delete'
            ],
        ],

        'confirm_themes' => [
            \frontend\controllers\educational\PitchController::class => [
                'index',
                'confirm-theme',
                'decline-theme'
            ]
        ]
    ];

    // системные экшны, которые не должны учитываться при мониторинге
    private $systemActions = [
        \frontend\controllers\SiteController::class => [
            'index',
            'login',
            'logout',
        ],
    ];

    public function getPermissionsName()
    {
        return array_keys($this->permissionActionLinks);
    }

    public function getAllPermissions()
    {
        return $this->permissionActionLinks;
    }

    public function getAllControllers() {
        $keys = [];

        foreach ($this->permissionActionLinks as $group => $controllers) {
            $controllerKeys = array_keys($controllers);
            $keys = array_merge($keys, $controllerKeys);
        }

        return $keys;
    }

    public function getAllActionsByController($controllerName)
    {
        $actions = [];
        foreach ($this->permissionActionLinks as $permission) {
            if (array_key_exists($controllerName, $permission)) {
                $actions = array_merge($actions, $permission[$controllerName]);
            }
        }

        if (array_key_exists($controllerName, $this->systemActions)) {
            $actions = array_diff($this->systemActions[$controllerName], $actions);
        }

        return $actions;
    }
}