<?php

use common\helpers\StringFormatter;
use frontend\forms\certificate\CertificateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

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
            ['attribute' => 'number', 'format' => 'raw', 'label' => 'Номер сертификата', 'value' => function (CertificateForm $model) {
                return $model->entity->getCertificateLongNumber();
            }],
            ['attribute' => 'template', 'format' => 'raw', 'label' => 'Наименование шаблона', 'value' => function (CertificateForm $model) {
                return $model->entity->certificateTemplatesWork->name;
            }],
            ['attribute' => 'participant', 'format' => 'raw', 'label' => 'Учащийся', 'value' => function (CertificateForm $model) {
                return StringFormatter::stringAsLink(
                    $model->entity->trainingGroupParticipantWork->getFullFio(),
                    Url::to(['/dictionaries/foreign-event-participants/view', 'id' => $model->entity->trainingGroupParticipantWork->participant_id])
                );
            }],
            ['attribute' => 'group', 'format' => 'raw', 'label' => 'Учебная группа', 'value' => function (CertificateForm $model) {
                return StringFormatter::stringAsLink(
                    $model->entity->trainingGroupParticipantWork->trainingGroupWork->number,
                    Url::to(['/educational/training-group/view', 'id' => $model->entity->trainingGroupParticipantWork->training_group_id])
                );
            }],
            ['attribute' => 'pdfFile', 'format' => 'raw', 'label' => 'Файлы', 'value' => function (CertificateForm $model) {
                // Используем стандартные классы Bootstrap для разделения кнопок (btn-block или маргины)
                return Html::a(
                        "Скачать pdf-файл",
                        Url::to(['generation-pdf', 'id' => $model->id]),
                        ['class'=>'btn btn-success mb-2', 'style' => 'margin-bottom: 5px;'] // mb-2 для BS4/5, style-костыль для BS3 облегчен
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

    <?php if (Yii::$app->rubac->checkPermission(Yii::$app->rubac->authId(), 'delete_certificates')): ?>
        <div class="certificate-template-change table-responsive" style="margin-top: 30px;">
            <h3>Изменить тип сертификата</h3>

            <?php $form = ActiveForm::begin([
                'action' => ['change-template', 'id' => $model->id],
                'method' => 'post',
            ]); ?>

            <div class="input-group">
                <?= $form->field($model, 'templateId', ['options' => ['tag' => false]])->dropDownList(
                    ArrayHelper::map($model->templates, 'id', 'name'),
                    ['prompt' => 'Выберите шаблон...', 'class' => 'form-control']
                )->label(false) ?>

                <span class="input-group-btn">
                    <?= Html::submitButton('Изменить шаблон', [
                        'class' => 'btn btn-warning',
                        'data' => [
                            'confirm' => 'Вы действительно хотите изменить тип сертификата?',
                        ],
                    ]) ?>
                </span>

            </div>

            <?php ActiveForm::end(); ?>
        </div>
    <?php endif; ?>

</div>