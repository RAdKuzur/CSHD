<?php

use common\components\dictionaries\base\DocumentStatusDictionary;
use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use yii\widgets\ActiveForm;

/* @var $searchModel \frontend\models\search\SearchDocumentIn */

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
        SearchFieldHelper::textField('executorName', 'Ответственный', 'Ответственный'),
        SearchFieldHelper::dropdownField('sendMethod', 'Способ получения', Yii::$app->sendMethods->getList(), 'Способ получения'),
        SearchFieldHelper::dropdownField('status', 'Статус документа', Yii::$app->documentStatus->getListDocIn(), null, DocumentStatusDictionary::CURRENT)
    );

    echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::DOC_IN_INDEX); ?>

<?php ActiveForm::end(); ?>