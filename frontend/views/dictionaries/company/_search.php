<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\search\SearchCompany;
use yii\widgets\ActiveForm;

/* @var $searchModel SearchCompany */
?>


<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>

<?php

$searchFields = array_merge(
    SearchFieldHelper::textField('inn', 'ИНН', 'ИНН'),
    SearchFieldHelper::textField('name' , 'Полное или краткое наименование', 'Полное или краткое наименование'),
    SearchFieldHelper::dropdownField('type', 'Тип организации', Yii::$app->companyType->getList(), 'Тип организации'),
    SearchFieldHelper::dropdownField('is_contractor', 'Контрагент', $searchModel::CONTRACTOR , 'Контрагент'),
);

echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 2, Yii::$app->frontUrls::COMPANY_INDEX); ?>

<?php ActiveForm::end(); ?>
