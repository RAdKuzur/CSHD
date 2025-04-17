<?php

use common\helpers\html\HtmlBuilder;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\search\SearchFieldHelper;


///* @var $this yii\web\View */
/* @var $searchModel \frontend\models\search\SearchLocalResponsibility */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>


<?php
    $searchFields = array_merge(
        SearchFieldHelper::dropdownField("responsibilityTypeStr", "Вид ответственности", Yii::$app->responsibilityType->getList(), "Вид ответственности"),
        SearchFieldHelper::dropdownField("branchStr", "Отдел", Yii::$app->branches->getList(), "Отдел"),
        SearchFieldHelper::textField("auditoriumStr", "Помещение", "Помещение"),
        SearchFieldHelper::textField("peopleStampStr", "Работник", "Работник"),
        SearchFieldHelper::textField("regulationStr", "Положение", "Положение"),
    );


echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 2, Yii::$app->frontUrls::LOCAL_RESPONSIBILITY_INDEX);?>

<?php ActiveForm::end(); ?>