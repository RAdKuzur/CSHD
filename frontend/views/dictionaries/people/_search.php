<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\search\SearchPeople;
use yii\widgets\ActiveForm;

/* @var $searchModel SearchPeople */
?>

<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>

<?php

$searchFields = array_merge(
    SearchFieldHelper::textField('surname', 'Фамилия', 'Фамилия'),
    SearchFieldHelper::textField('name' , 'Имя', 'Имя'),
    SearchFieldHelper::textField('patronymic', 'Отчетство', 'Отчетство'),
    SearchFieldHelper::textField('organized', 'Должность', 'Должность'),
    SearchFieldHelper::textField('position', 'Организация', 'Организация'),
);

echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::PEOPLE_INDEX); ?>

<?php ActiveForm::end(); ?>
