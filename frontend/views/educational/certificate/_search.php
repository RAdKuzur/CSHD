
<?php
use common\helpers\html\HtmlBuilder;
use yii\widgets\ActiveForm;
use common\helpers\search\SearchFieldHelper;

/* @var $searchModel \frontend\models\search\SearchCertificate */

?>

<?php
$form = ActiveForm::begin([
    'action' => ['index'], // Действие контроллера для обработки поиска
    'method' => 'get', // Метод GET для передачи параметров в URL
    'options' => ['data-pjax' => true], // Для использования Pjax
]);
?>


<?php
    $searchFields = array_merge(
        SearchFieldHelper::textField("certificateNumStr", "Номер сертификата", "Номер сертификата"),
        SearchFieldHelper::textField("certificateTemplateStr", "Наименование шаблона", "Наименование шаблона"),
        SearchFieldHelper::textField("participantStr", "Учащийся", "Учащийся"),
        SearchFieldHelper::textField("trainingGroupStr", "Учебная группа", "Учебная группа"),
        SearchFieldHelper::dateField("startProtectionDate", "Дата защиты с", "Дата защиты с"),
        SearchFieldHelper::dateField("endProtectionDate", "Дата защиты до", "Дата защиты до"),
    );

echo HtmlBuilder::createFilterPanel($searchModel,$searchFields, $form, 2, Yii::$app->frontUrls::CERTIFICATE_INDEX); ?>


<?php ActiveForm::end(); ?>

