<?php

use frontend\forms\training_group\ProtocolForm;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ProtocolForm */
/* @var $form ActiveForm */
?>

<?php
$this->title = 'Протокол итоговой аттестации ' . $model->getNumberGroup();
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Группа ' . $model->getNumberGroup(), 'url' => [Yii::$app->frontUrls::TRAINING_GROUP_VIEW, 'id' => $model->group->id]];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="substrate">
    <h3><?= Html::encode($this->title) ?></h3>
</div>

<div class="man-hours-report-form field-backing">

    <label><b>Выберите название публичного мероприятия или введите его вручную</b></label>

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->field($model, 'name')->textInput(['value' => 'Научно-техническая конференция SchoolTech Conference',
        'placeholder' => 'Демонстрация результатов образовательной деятельности'])->label(false) ?>

    <br>
    <label><b>Выделите всех присутствовавших на защите:</b></label><br>
    <div class="checkbox-list">
        <?= $form->field($model, 'participants')->checkboxList(
            ArrayHelper::map($model->possibleParticipants, 'id', function (TrainingGroupParticipantWork $participant) {
                return $participant->participantWork->getFIO(PersonInterface::FIO_FULL);
            }),
            [
                'item' => function ($index, $label, $name, $checked, $value) {
                    return Html::checkbox($name, $checked, [
                        'value' => $value,
                        'label' => $label,
                        'checked' => true,
                    ]);
                },
            ]
        )->label(false) ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Скачать отчет', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>