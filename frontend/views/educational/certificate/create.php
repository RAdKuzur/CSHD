<?php

use frontend\forms\certificate\CertificateForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model CertificateForm */

$this->title = 'Создать сертификат';
$this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="certificate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
