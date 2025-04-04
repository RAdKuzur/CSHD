<?php

use common\repositories\educational\TrainingGroupRepository;
use frontend\components\GroupParticipantWidget;
use frontend\forms\certificate\CertificateForm;
use frontend\models\work\educational\CertificateWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model CertificateForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="certificate-form">

    <div style="margin: 0 103%;">
        <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Если обучающийся не отображен в списке проверьте следующие возможные причины:
                                                            &#10   &#10102 У обучающегося уже есть сертификат об окончании обучения в данной учебной группе
                                                            &#10   &#10103 Обучающийся отчислен из учебной группы
                                                            &#10   &#10104 У обучающегося отсутствует галочка успешного окончания в журнале" >❔</div>
    </div>

    <?php $form = ActiveForm::begin([
        'options' => ['target' => '_blank', 'id' => 'form1']
    ]); ?>

    <?= $form->field($model, 'templateId')->dropDownList(
            ArrayHelper::map($model->templates, 'id', 'name')
        )
    ?>

    <?= GroupParticipantWidget::widget([
        'config' => [
            'groupUrl' => 'get-groups',
            'participantUrl' => 'get-participants'
        ],
        'dataProviderGroup' => new ActiveDataProvider([
            'query' => $model->groupQuery
        ]),
        'dataProviderParticipant' => new ActiveDataProvider([
            'query' => $model->participantQuery
        ]),
    ]);
    ?>

    <div class="form-group">
        <?php
        echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>