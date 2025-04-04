<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use yii\widgets\ActiveForm;

/* @var $searchModel \frontend\models\search\SearchTrainingGroup */

?>

<div class="training-group-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'], // Действие контроллера для обработки поиска
        'method' => 'get', // Метод GET для передачи параметров в URL
        'options' => ['data-pjax' => true], // Для использования Pjax
    ]); ?>

    <?php
    $searchFields = array_merge(
        SearchFieldHelper::dateField('startDateSearch', 'Дата обучения с', 'Дата обучения с'),
        SearchFieldHelper::dateField('finishDateSearch', 'Дата обучения по', 'Дата обучения по'),
        SearchFieldHelper::dropdownField('branch', 'Отдел', Yii::$app->branches->getOnlyEducational(), 'Отдел'),
        SearchFieldHelper::textField('numberPretty', 'Номер или часть номера группы', 'Номер или часть номера группы'),
        SearchFieldHelper::textField('teacher', 'Преподаватель', 'Преподаватель'),
        SearchFieldHelper::textField('program', 'Образовательная программа', 'Образовательная программа'),
        SearchFieldHelper::dropdownField('budget', 'Источник финансирования', $searchModel::BUDGET, 'Источник финансирования'),
        SearchFieldHelper::dropdownField('archive', 'Статус', $searchModel::ARCHIVE, 'Статус'),
    );

    echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::TRAINING_GROUP_INDEX); ?>

    <?php ActiveForm::end(); ?>

</div>
