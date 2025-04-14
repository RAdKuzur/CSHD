<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\search\SearchAuditorium;
use yii\widgets\ActiveForm;

/* @var $searchModel SearchAuditorium */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>

<?php

$searchFields = array_merge(
    SearchFieldHelper::textField('globalNumber', 'Глобальный номер', 'Глобальный номер'),
    SearchFieldHelper::textField('name' , 'Имя', 'Имя'),
    SearchFieldHelper::textField('square', 'Площадь', 'Площадь'),
    SearchFieldHelper::dropdownField('type', 'Тип помещения', Yii::$app->auditoriumType->getList(), 'Тип помещения'),
    SearchFieldHelper::dropdownField('is_education', 'Для образовательной деятельности', $searchModel::EDUCATIONAL , 'Для образовательной деятельности'),
    SearchFieldHelper::dropdownField('branch', 'Отдел', Yii::$app->branches->getList(), 'Отдел'),
);

echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::AUDITORIUM_INDEX); ?>

<?php ActiveForm::end(); ?>