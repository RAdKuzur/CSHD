<?php

use common\helpers\StringFormatter;
use frontend\forms\certificate\CertificateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model CertificateForm */

$this->title = 'Сертификат № '. $model->entity->getCertificateLongNumber();
$this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="certificate-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if (Yii::$app->rubac->checkPermission(Yii::$app->rubac->authId(), 'delete_certificates')) {
            echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы действительно хотите удалить сертификат?',
                    'method' => 'post',
                ],
            ]);
        }
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            ['attribute' => 'number', 'format' => 'raw', 'value' => function (CertificateForm $model) {
                return $model->entity->getCertificateLongNumber();
            }],
            ['attribute' => 'template', 'format' => 'raw', 'value' => function (CertificateForm $model) {
                return $model->entity->certificateTemplatesWork->name;
            }],
            ['attribute' => 'participant', 'format' => 'raw', 'value' => function (CertificateForm $model) {
                return StringFormatter::stringAsLink(
                    $model->entity->trainingGroupParticipantWork->getFullFio(),
                    Url::to(['/dictionaries/foreign-event-participants/view', 'id' => $model->entity->trainingGroupParticipantWork->participant_id])
                );
            }],
            ['attribute' => 'group', 'format' => 'raw', 'value' => function (CertificateForm $model) {
                return StringFormatter::stringAsLink(
                    $model->entity->trainingGroupParticipantWork->trainingGroupWork->number,
                    Url::to(['/educational/training-group/view', 'id' => $model->entity->trainingGroupParticipantWork->training_group_id])
                );
            }],
            ['attribute' => 'pdfFile', 'format' => 'raw', 'value' => function (CertificateForm $model) {
                return Html::a(
                        "Скачать pdf-файл",
                        Url::to(['generation-pdf', 'id' => $model->id]),
                        ['class'=>'btn btn-success', 'style' => 'margin-bottom: 8px']
                    ).
                    '<br>'.
                    Html::a(
                        "Отправить pdf-файл по e-mail",
                        Url::to(['send-pdf', 'id' => $model->id]),
                        ['class'=>'btn btn-primary']
                    );
            }],
        ],
    ]) ?>

</div>
