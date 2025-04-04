<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\work\regulation\RegulationWork;
use yii\widgets\ActiveForm;

/* @var $searchModel \frontend\models\search\SearchRegulationEvent */

?>

<div class="regulation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'], // Действие контроллера для обработки поиска
        'method' => 'get', // Метод GET для передачи параметров в URL
        'options' => ['data-pjax' => true], // Для использования Pjax
    ]); ?>

    <?php
    $searchFields = array_merge(
        SearchFieldHelper::dateField('startDateSearch', 'Дата положения с', 'Дата положения с'),
        SearchFieldHelper::dateField('finishDateSearch', 'Дата положения по', 'Дата положения по'),
        SearchFieldHelper::textField('nameRegulation' , 'Наименование документа', 'Наименование документа'),
        SearchFieldHelper::textField('orderName', 'Номер или наименование приказа', 'Наименование приказа'),
        SearchFieldHelper::dropdownField('status', 'Статус положения', RegulationWork::states(), 'Состояние положения')
    );

    echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::REG_EVENT_INDEX); ?>

    <?php ActiveForm::end(); ?>
</div>
