<?php

use common\components\dictionaries\base\DocumentStatusDictionary;
use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use yii\widgets\ActiveForm;

/* @var $searchModel \frontend\models\search\SearchDocumentOut */

?>

<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>

<?php
$searchFields = array_merge(
    SearchFieldHelper::dateField('startDateSearch', 'Дата документа с', 'Дата документа с'),
    SearchFieldHelper::dateField('finishDateSearch', 'Дата документа по', 'Дата документа по'),
    SearchFieldHelper::textField('number' , 'Номер документа', 'Номер документа'),
    SearchFieldHelper::textField('documentTheme', 'Тема документа', 'Тема документа'),
    SearchFieldHelper::textField('keyWords', 'Ключевые слова', 'Ключевые слова'),
    SearchFieldHelper::textField('correspondentName', 'Корреспондент', 'Корреспондент'),
    SearchFieldHelper::textField('executorName', 'Исполнитель/Кем подписан', 'Исполнитель/Кем подписан'),
    SearchFieldHelper::dropdownField('sendMethod', 'Способ получения', Yii::$app->sendMethods->getList(), 'Способ получения'),
    SearchFieldHelper::dropdownField('status', 'Статус документа', Yii::$app->documentStatus->getListDocOut(), null, DocumentStatusDictionary::CURRENT)
);

echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::DOC_OUT_INDEX); ?>

<?php ActiveForm::end(); ?>