<?php

namespace frontend\components\routes;

class Urls
{
    /**
     * Константы DocumentIn
     *
     * DOC_IN_VIEW - actionView
     * DOC_IN_CREATE - actionCreate
     * DOC_IN_RESERVE - actionReserve
     * DOC_IN_INDEX - actionIndex
    */
    const DOC_IN_VIEW = "document/document-in/view";
    const DOC_IN_CREATE = "document/document-in/create";
    const DOC_IN_RESERVE = "document/document-in/reserve";
    const DOC_IN_INDEX = "document/document-in/index";

    /**
     * Константы DocumentOut
     *
     * DOC_OUT_VIEW - actionView
     * DOC_OUT_CREATE - actionCreate
     * DOC_OUT_INDEX - actionIndex
     */
    const DOC_OUT_VIEW = "document/document-out/view";
    const DOC_OUT_CREATE = "document/document-out/create";
    const DOC_OUT_INDEX = "document/document-out/index";

    /**
     * Константы RegulationEvent
     *
     * REG_EVENT_VIEW - actionView
     * REG_EVENT_INDEX - actionIndex
     */
    const REG_EVENT_INDEX = "regulation/regulation-event/index";
    const REG_EVENT_VIEW = "regulation/regulation-event/view";

    /**
     * Константы Regulation
     *
     * REG_VIEW - actionView
     * REG_INDEX - actionIndex
     */
    const REG_INDEX = "regulation/regulation/index";
    const REG_VIEW = "regulation/regulation/view";

    /**
     * Константы OurEvent
     *
     * OUR_EVENT_VIEW - actionView
     * OUR_EVENT_INDEX - actionIndex
     */
    const OUR_EVENT_INDEX = "event/our-event/index";
    const OUR_EVENT_VIEW = "event/our-event/view";

    /**
     * Константы ForeignEvent
     *
     * FOREIGN_EVENT_VIEW - actionView
     * FOREIGN_EVENT_INDEX - actionIndex
     */
    const FOREIGN_EVENT_VIEW = "event/foreign-event/view";
    const FOREIGN_EVENT_INDEX = "event/foreign-event/index";

    /**
     * Константы TrainingProgram
     *
     * PROGRAM_VIEW - actionView
     * PROGRAM_INDEX - actionIndex
     * PROGRAM_RELEVANCE - actionRelevance
     */
    CONST PROGRAM_INDEX = "educational/training-program/index";
    const PROGRAM_VIEW = "educational/training-program/view";
    const PROGRAM_RELEVANCE = "educational/training-program/relevance";

    /**
     * Константа для пост запроса изменения актуальности TrainingProgram и TrainingGroup
     */
    const ACTUAL_OBJECT = "@app/views/educational/relevance-post/relevance-post.php";

    /**
     * Константы TrainingGroup
     *
     * TRAINING_GROUP_VIEW - actionView
     * TRAINING_GROUP_UPDATE - actionView
     * TRAINING_GROUP_INDEX - actionIndex
     * TRAINING_GROUP_ARCHIVE - actionRelevance
     * LESSON_THEMES_CREATE - actionCreateLessonThemes
     * JOURNAL_DELETE - actionDeleteJournal
     */
    const TRAINING_GROUP_INDEX = "educational/training-group/index";
    const TRAINING_GROUP_UPDATE = "educational/training-group/base-form";
    const TRAINING_GROUP_VIEW = "educational/training-group/view";
    const TRAINING_GROUP_ARCHIVE = "educational/training-group/archive";
    const LESSON_THEMES_CREATE = "educational/training-group/create-lesson-themes";
    const JOURNAL_DELETE = "educational/training-group/delete-journal";
    const PITCH = "educational/pitch";

    /**
     * Константы TrainingProgram
     *
     * TRAINING_PROGRAM_VIEW - actionView
     */
    const TRAINING_PROGRAM_VIEW = "educational/training-program/view";

    /**
     * Константы Journal
     *
     * JOURNAL_VIEW - actionView
     * JOURNAL_UPDATE - actionUpdate
     * JOURNAL_UPDATE - actionEditPlan
     * JOURNAL_DELETE_PLAN - actionDeletePlan
     */
    const JOURNAL_VIEW = "educational/journal/view";
    const JOURNAL_UPDATE = "educational/journal/update";
    const JOURNAL_EDIT_PLAN = "educational/journal/edit-plan";
    const JOURNAL_DELETE_PLAN = "educational/journal/delete-plan";

    /**
     * Константы Certificate
     *
     * CERTIFICATE_VIEW - actionView
     * CERTIFICATE_INDEX - actionIndex
     */
    const CERTIFICATE_VIEW = "educational/certificate/view";
    const CERTIFICATE_INDEX = "educational/certificate/index";

    /**
     * Константы ForeignEventParticipants
     *
     * PARTICIPANT_VIEW - actionView
     * PARTICIPANT_FILE_LOAD - actionFileLoad
     */
    const PARTICIPANT_VIEW = "dictionaries/foreign-event-participants/view";
    const PARTICIPANT_FILE_LOAD = "dictionaries/foreign-event-participants/file-load";

    /**
     * Константы People
     *
     * PEOPLE_VIEW - actionView
     * PEOPLE_INDEX - actionIndex
     */
    const PEOPLE_VIEW = "dictionaries/people/view";
    const PEOPLE_INDEX = "dictionaries/people/index";

    /**
     * Константы Company
     *
     * COMPANY_VIEW - actionView
     * COMPANY_INDEX - actionIndex
     */
    const COMPANY_VIEW = "dictionaries/company/view";
    const COMPANY_INDEX = "dictionaries/company/index";

    /**
     * Константы Position
     *
     * POSITION_VIEW - actionView
     * POSITION_INDEX - actionIndex
     */
    const POSITION_VIEW = "dictionaries/position/view";
    const POSITION_INDEX = "dictionaries/position/index";

    /**
     * Константы Auditorium
     *
     * AUDITORIUM_VIEW - actionView
     * AUDITORIUM_INDEX - actionIndex
     */
    const AUDITORIUM_VIEW = "dictionaries/auditorium/view";
    const AUDITORIUM_INDEX = "dictionaries/auditorium/index";

    /**
     * Константы LocalResponsibility
     *
     * LOCAL_RESPONSIBILITY_VIEW - actionView
     * LOCAL_RESPONSIBILITY_INDEX - actionIndex
     */
    const LOCAL_RESPONSIBILITY_VIEW = "responsibility/local-responsibility/view";
    const LOCAL_RESPONSIBILITY_INDEX = "responsibility/local-responsibility/index";

    /**
     * Константы OrderMain
     *
     * ORDER_MAIN_VIEW - actionView
     * ORDER_MAIN_RESERVE - actionReserve
     * ORDER_INDEX - actionIndex
     */
    const ORDER_MAIN_VIEW = "order/order-main/view";
    const ORDER_MAIN_RESERVE = "order/order-main/reserve";
    const ORDER_MAIN_INDEX = "order/order-main/index";

    /**
     * Константы OrderTraining
     *
     * ORDER_TRAINING_VIEW - actionView
     * ORDER_TRAINING_INDEX - actionIndex
     */
    const ORDER_TRAINING_VIEW = "order/order-training/view";
    const ORDER_TRAINING_INDEX = "order/order-training/index";

    /**
     * Константы OrderEvent
     *
     * ORDER_EVENT_VIEW - actionView
     * ORDER_EVENT_INDEX - actionIndex
     */
    const ORDER_EVENT_VIEW = "order/order-event/view";
    const ORDER_EVENT_INDEX = "order/order-event/index";

    /**
     *
     */
    const ANALITIC_ERRORS_INDEX = "analytics/errors";
}