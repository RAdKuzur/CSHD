<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use yii\widgets\ActiveForm;

/* @var $searchModel \frontend\models\search\SearchTrainingGroup */

?>

<div class="training-group-search-relevance">

    <?php $form = ActiveForm::begin([
        'action' => ['archive'], // Действие контроллера для обработки поиска
        'method' => 'get', // Метод GET для передачи параметров в URL
        'options' => ['data-pjax' => true], // Для использования Pjax
    ]); ?>

    <?php
    $toggle = HtmlBuilder::createToggle(
                'Не архивные группы',
                'Архивные<br> группы',
                'archive',
                'SearchTrainingGroup[archive]',
                (bool)$searchModel->archive
    );

    $searchFields = array_merge(
        SearchFieldHelper::specialHtmlFiled('archive', $toggle),
        SearchFieldHelper::dateField('startDateSearch', 'Дата обучения с', 'Дата обучения с'),
        SearchFieldHelper::dateField('finishDateSearch', 'Дата обучения по', 'Дата обучения по'),
        SearchFieldHelper::dropdownField('branch', 'Отдел', Yii::$app->branches->getOnlyEducational(), 'Отдел'),
        SearchFieldHelper::textField('numberPretty', 'Номер или часть номера группы', 'Номер или часть номера группы'),
        SearchFieldHelper::textField('teacher', 'Преподаватель', 'Преподаватель'),
        SearchFieldHelper::textField('program', 'Образовательная программа', 'Образовательная программа'),
        SearchFieldHelper::dropdownField('budget', 'Источник финансирования', $searchModel::BUDGET, 'Источник финансирования'),
    );

    echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::TRAINING_GROUP_ARCHIVE); ?>

    <?php ActiveForm::end(); ?>

</div>
