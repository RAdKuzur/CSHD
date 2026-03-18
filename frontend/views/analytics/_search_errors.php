<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\search\SearchErrors;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $searchModel SearchErrors */
/* @var $branches array */
/* @var $activeTab string */

$id = Yii::$app->request->get('id');

?>
<div class="errors-search">
    <?php $form = ActiveForm::begin([
        'action' => ['analytics/errors', 'id' => $id],
        'method' => 'get',
        'options' => ['data-pjax' => true],
    ]); ?>

<?= Html::hiddenInput('tab', $activeTab, ['id' => 'activeTabInput']) ?>

    <?php
    $searchFields = array_merge(
        SearchFieldHelper::textField('error_code', 'Код ошибки', 'Введите код ошибки'),
        SearchFieldHelper::textField('error_description', 'Описание проблемы', 'Введите описание'),
        SearchFieldHelper::dateField('create_date_from', 'Дата с', 'Дата начала'),
        SearchFieldHelper::dateField('create_date_to', 'Дата по', 'Дата окончания'), 
        SearchFieldHelper::dropdownField('branch', 'Отдел', Yii::$app->branches->getOnlyEducational(), 'Отдел'),
        SearchFieldHelper::textField('entity_name', 'Название сущности', 'Введите место возникновения'),
    );
$resetUrl = ['analytics/errors', 'id' => $id,'tab' => $activeTab];

echo HtmlBuilder::createFilterPanel( $searchModel, $searchFields, $form, 3, $resetUrl); ?>

<?php ActiveForm::end(); ?>

</div>