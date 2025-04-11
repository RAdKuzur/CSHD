<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\search\SearchForeignEvent;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel SearchForeignEvent */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="foreign-event-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'], // Действие контроллера для обработки поиска
        'method' => 'get', // Метод GET для передачи параметров в URL
        'options' => ['data-pjax' => true], // Для использования Pjax
    ]); ?>

    <?php
    $searchFields = array_merge(
        SearchFieldHelper::dateField('startDateSearch', 'Дата учета достижения с', 'Дата учета достижения с'),
        SearchFieldHelper::dateField('finishDateSearch', 'Дата учета достижения по', 'Дата учета достижения по'),
        SearchFieldHelper::textField('eventName' , 'Наименование мероприятия', 'Наименование мероприятия'),
        SearchFieldHelper::dropdownField('eventLevel', 'Уровень мероприятия', Yii::$app->eventLevel->getList(), 'Уровень мероприятия'),
        SearchFieldHelper::dropdownField('eventWay', 'Формат проведения', Yii::$app->eventWay->getList(), 'Формат проведения'),
        SearchFieldHelper::textField('city', 'Город мероприятия', 'Город мероприятия'),
        SearchFieldHelper::textField('organizerName', 'Организатор', 'Организатор'),
        SearchFieldHelper::textField('nameParticipant', 'Фамилия участника', 'Фамилия участника'),
        SearchFieldHelper::textField('nameTeacher', 'Фамилия педагога', 'Фамилия педагога'),
        SearchFieldHelper::dropdownField('branch', 'Отдел', Yii::$app->branches->getOnlyEducational(), 'Отдел'),

        SearchFieldHelper::textField('keyWord', 'Ключевые слова', 'Ключевые слова'),
    );

    echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::REG_EVENT_INDEX); ?>

    <?php ActiveForm::end(); ?>

</div>
