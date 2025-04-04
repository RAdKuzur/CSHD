<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\work\educational\training_program\TrainingProgramWork;
use yii\widgets\ActiveForm;

/* @var $searchModel \frontend\models\search\SearchTrainingProgram */

?>

<div class="training-program-search-relevance">

    <?php $form = ActiveForm::begin([
        'action' => ['relevance'], // Действие контроллера для обработки поиска
        'method' => 'get', // Метод GET для передачи параметров в URL
        'options' => ['data-pjax' => true], // Для использования Pjax
    ]); ?>

    <?php
    $toggle = HtmlBuilder::createToggle(
                'Не актуальные программы',
                'Актуальные программы',
                'actual',
                'SearchTrainingProgram[actualRelevance]',
                (bool)$searchModel->actualRelevance
    );

    $searchFields = array_merge(
        SearchFieldHelper::specialHtmlFiled('actual', $toggle),
        SearchFieldHelper::dateField('startDateSearch', 'Дата пед.совета с', 'Дата пед.совета с'),
        SearchFieldHelper::dateField('finishDateSearch', 'Дата пед.совета по', 'Дата пед.совета по'),
        SearchFieldHelper::dropdownField('branchSearch', 'Место реализации', Yii::$app->branches->getOnlyEducational(), 'Место реализации'),
        SearchFieldHelper::textField('programName', 'Наименование программы', 'Наименование программы'),
        SearchFieldHelper::textField('authorSearch', 'Составитель', 'Составитель'),
        SearchFieldHelper::dropdownField('levelSearch', 'Уровень сложности', TrainingProgramWork::LEVEL_LIST, 'Уровень сложности'),
        SearchFieldHelper::dropdownField('focusSearch', 'Направленность', Yii::$app->focus->getList(), 'Направленность'),
    );

    echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::PROGRAM_RELEVANCE); ?>

    <?php ActiveForm::end(); ?>

</div>
