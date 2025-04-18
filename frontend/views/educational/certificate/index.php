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

/* @var $this yii\web\View */
/* @var $searchModel SearchCertificate */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="certificat-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
        <p>
            <?php
            echo Html::a('Добавить сертифкат(-ы)', ['create'], ['class' => 'btn btn-success'])
            ?>
        </p>

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
        'columns' => [
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

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
