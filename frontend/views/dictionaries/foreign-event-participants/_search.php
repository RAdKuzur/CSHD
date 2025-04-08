<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\search\SearchForeignEventParticipants;
use yii\widgets\ActiveForm;

/* @var $searchModel SearchForeignEventParticipants */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>

<?php
$searchFields = array_merge(
    SearchFieldHelper::textField('participantName' , 'ФИО учащегося', 'ФИО учащегося'),
    /*SearchFieldHelper::dropdownField('branch', 'Отдел обучения', Yii::$app->branches->getOnlyEducational(), 'Отдел обучения'),
    SearchFieldHelper::dropdownField('branch', 'Отдел обучения', Yii::$app->branches->getOnlyEducational(), 'Отдел обучения'),
    SearchFieldHelper::dropdownField('branch', 'Отдел обучения', Yii::$app->branches->getOnlyEducational(), 'Отдел обучения'),*/
);

echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::PARTICIPANT_INDEX); ?>

<?php ActiveForm::end(); ?>