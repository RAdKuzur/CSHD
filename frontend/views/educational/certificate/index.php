<?php

use common\helpers\DateFormatter;
use common\helpers\html\HtmlCreator;
use common\helpers\StringFormatter;
use frontend\models\search\SearchCertificate;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\general\PeopleWork;
use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel SearchCertificate */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $templates array */ // Добавьте это в контроллере

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="certificat-index">

        <div class="substrate">
            <h1><?= Html::encode($this->title) ?></h1>

            <div class="flexx space">
                <div class="flexx">
                    <?php
                    echo Html::a('Добавить сертифкат(-ы)', ['create'], ['class' => 'btn btn-success'])
                    ?>
                </div>

                <!-- Форма для массового изменения шаблона -->
                <?php if (Yii::$app->rubac->checkPermission(Yii::$app->rubac->authId(), 'delete_certificates')): ?>
                    <div class="mass-template-change">
                        <?php $form = ActiveForm::begin([
                            'action' => ['mass-change-template'],
                            'method' => 'post',
                            'id' => 'mass-template-form',
                        ]); ?>

                        <?= Html::hiddenInput('certificate_ids', '', ['id' => 'certificate-ids']) ?>

                        <div class="input-group">
                            <?= Html::dropDownList(
                                'template_id',
                                null,
                                ArrayHelper::map($templates, 'id', 'name'),
                                [
                                    'prompt' => 'Выберите новый шаблон...',
                                    'class' => 'form-control',
                                    'id' => 'template-select'
                                ]
                            ) ?>

                            <span class="input-group-btn">
                <?= Html::submitButton('Изменить шаблон', [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Вы действительно хотите изменить тип сертификата у выбранных записей?',
                    ],
                    'disabled' => true,
                    'id' => 'submit-mass-change'
                ]) ?>
            </span>

                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                <?php endif; ?>

                <div style="margin-bottom: 10px;">
                    <?php

                    $gridColumns = [
                        ['attribute' => 'certificate_number', 'format' => 'raw', 'value' => function(CertificateWork $model){
                            return $model->getCertificateLongNumber();
                        }],
                        ['attribute' => 'certificate_template_id', 'format' => 'raw', 'label' => 'Наименование шаблона', 'value' => function(CertificateWork $model){
                            return $model->certificateTemplatesWork->name;
                        }],
                        ['attribute' => 'participant_id', 'format' => 'raw', 'label' => 'Учащийся', 'value' => function(CertificateWork $model){
                            if ($model->trainingGroupParticipantWork && $model->trainingGroupParticipantWork->participantWork) {
                                return $model->trainingGroupParticipantWork->participantWork->getFIO(PersonInterface::FIO_FULL);
                            }
                            return '';
                        }],
                        ['attribute' => 'training_group_id', 'format' => 'raw', 'label' => 'Учебная группа', 'value' => function(CertificateWork $model){
                            if ($model->trainingGroupParticipantWork && $model->trainingGroupParticipantWork->trainingGroupWork) {
                                return $model->trainingGroupParticipantWork->trainingGroupWork->number;
                            }
                            return '';
                        }],
                        ['attribute' => 'protection_date', 'label' => 'Дата защиты', 'format' => 'raw', 'value' => function(CertificateWork $model){
                            if ($model->trainingGroupParticipantWork && $model->trainingGroupParticipantWork->trainingGroupWork) {
                                return $model->trainingGroupParticipantWork->trainingGroupWork->protection_date;
                            }
                            return '';
                        }],

                    ];
                    //                echo ExportMenu::widget([
                    //                    'dataProvider' => $dataProvider,
                    //                    'columns' => $gridColumns,
                    //                    'options' => [
                    //                        'padding-bottom: 100px',
                    //                    ]
                    //                ]);

                    ?>
                </div>
                <?= HtmlCreator::filterToggle() ?>
            </div>
        </div>

        <?= $this->render('_search', ['searchModel' => $searchModel]) ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['id' => 'certificate-grid'],
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) {
                        return ['class' => 'certificate-checkbox'];
                    }
                ],
                ['class' => 'yii\grid\SerialColumn', 'header' => '№ п/п'],
                ['attribute' => 'certificate_number', 'format' => 'raw', 'value' => function(CertificateWork $model){
                    return StringFormatter::stringAsLink($model->getCertificateLongNumber(), Url::to(['view', 'id' => $model->id]));
                }],
                ['attribute' => 'certificate_template_id', 'label' => 'Наименование шаблона', 'format' => 'raw', 'value' => function(CertificateWork $model){
                    return StringFormatter::stringAsLink(
                        $model->certificateTemplatesWork->name,
                        Url::to(['/educational/certificate-template/view', 'id' => $model->certificate_template_id])
                    );
                }],
                ['attribute' => 'participantStr', 'format' => 'raw', 'label' => 'Учащийся', 'value' => function(CertificateWork $model){
                    if ($model->trainingGroupParticipantWork && $model->trainingGroupParticipantWork->participantWork) {
                        return StringFormatter::stringAsLink(
                            $model->trainingGroupParticipantWork->participantWork->getFIO(PersonInterface::FIO_FULL),
                            Url::to(['/dictionaries/foreign-event-participants/view', 'id' => $model->trainingGroupParticipantWork->participant_id])
                        );
                    }
                    return '';
                }],
                ['attribute' => 'trainingGroupStr', 'format' => 'raw', 'label' => 'Учебная группа', 'value' => function(CertificateWork $model){
                    if ($model->trainingGroupParticipantWork && $model->trainingGroupParticipantWork->trainingGroupWork) {
                        return StringFormatter::stringAsLink(
                            $model->trainingGroupParticipantWork->trainingGroupWork->number,
                            Url::to(['/educational/training-group/view', 'id' => $model->trainingGroupParticipantWork->training_group_id])
                        );
                    }
                    return '';
                }],
                ['attribute' => 'protectionDate', 'format' => 'raw', 'label' => 'Дата защиты', 'value' => function(CertificateWork $model){
                    if ($model->trainingGroupParticipantWork && $model->trainingGroupParticipantWork->trainingGroupWork) {
                        return DateFormatter::format(
                            $model->trainingGroupParticipantWork->trainingGroupWork->protection_date,
                            DateFormatter::Ymd_dash,
                            DateFormatter::dmY_dot
                        );
                    }
                    return '';
                }],
            ],
        ]); ?>

    </div>

<?php
// JavaScript для управления выбором сертификатов
$script = <<<JS
$(document).ready(function() {
    // Обновление скрытого поля при выборе чекбоксов
    function updateSelectedIds() {
        var selectedIds = [];
        $('.certificate-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        $('#certificate-ids').val(selectedIds.join(','));
        
        // Активируем/деактивируем кнопку отправки
        if (selectedIds.length > 0 && $('#template-select').val()) {
            $('#submit-mass-change').prop('disabled', false);
        } else {
            $('#submit-mass-change').prop('disabled', true);
        }
    }
    
    // Обработчики событий
    $(document).on('change', '.certificate-checkbox', updateSelectedIds);
    $('#template-select').on('change', updateSelectedIds);
    
    // Выбор всех чекбоксов
    $(document).on('change', '.select-on-check-all', function() {
        setTimeout(updateSelectedIds, 100);
    });
});
JS;
$this->registerJs($script);
?>