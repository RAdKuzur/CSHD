<?php

use common\helpers\html\HtmlBuilder;
use common\helpers\search\SearchFieldHelper;
use frontend\models\search\SearchPosition;
use yii\widgets\ActiveForm;

/* @var $searchModel SearchPosition */

?>


<?php $form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]); ?>

<?php

$searchFields = SearchFieldHelper::textField('name', 'Наименование должности', 'Наименование должности');

echo HtmlBuilder::createFilterPanel($searchModel, $searchFields, $form, 3, Yii::$app->frontUrls::POSITION_INDEX); ?>

<?php ActiveForm::end(); ?>
