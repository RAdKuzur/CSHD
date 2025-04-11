<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\search\SearchOrderTraining;
use yii\widgets\ActiveForm;

/* @var $searchModel SearchOrderTraining */

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>

<?php
$searchFields = array_merge(
    SearchFieldHelper::dateField('startDateSearch', 'Дата приказа с', 'Дата приказа с'),
    SearchFieldHelper::dateField('finishDateSearch', 'Дата приказа по', 'Дата приказа по'),
    SearchFieldHelper::textField('orderNumber' , 'Номер приказа', 'Номер приказа'),
    SearchFieldHelper::textField('orderName', 'Название приказа', 'Название приказа'),
    SearchFieldHelper::textField('keyWords', 'Ключевые слова', 'Ключевые слова'),
    SearchFieldHelper::textField('responsibleName', 'Фамилия ответственного', 'Фамилия ответственного'),
    SearchFieldHelper::textField('bringName', 'Фамилия кто вносит проект', 'Фамилия кто вносит проект'),
    SearchFieldHelper::textField('executorName', 'Фамилия исполнителя', 'Фамилия исполнителя'),
    //SearchFieldHelper::textField('participantName', 'Фамилия обучающегося', 'Фамилия обучающегося'),
    //SearchFieldHelper::textField('groupName', 'Название учебной группы', 'Название учебной группы'),
    SearchFieldHelper::dropdownField('branch', 'Отдел', Yii::$app->branches->getOnlyEducational(), 'Отдел'),
);

echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::ORDER_TRAINING_INDEX); ?>

<?php ActiveForm::end(); ?>