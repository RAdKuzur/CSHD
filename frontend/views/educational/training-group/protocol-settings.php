<?php

use common\components\dictionaries\base\BranchDictionary;
use frontend\forms\training_group\ProtocolForm;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\educational\training_group\TeacherGroupWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use frontend\models\work\general\PeopleWork;
use kartik\select2\Select2;
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

    <?php echo $form->field($model, 'name')->textInput(['value' => 'Научно-техническая конференция',
        'placeholder' => 'Демонстрация результатов образовательной деятельности'])->label(false) ?>


    <?php
    $peopleIds = [];

    switch ($model->group->branch) {
        case BranchDictionary::QUANTORIUM:
            $peopleIds = [29, 19];
            break;
        case BranchDictionary::TECHNOPARK:
            $peopleIds = [29, 946];
            break;
        case BranchDictionary::CDNTT:
            $peopleIds =[29, 21];
            break;
        case BranchDictionary::MOBILE_QUANTUM:
            $peopleIds = [29];
            break;
        case BranchDictionary::COD:
            $peopleIds = [29, 36];
            break;
    }

    // Получаем объекты людей по этим ID. Предположим, есть метод для этого
    $peopleWork = PeopleWork::findAll(['id' => $peopleIds]);
    ?>
    <br>
    <label><b>Выделите присутствовавшее ответственное лицо:</b></label><br>
    <div class="checkbox-list">
        <?= $form->field($model, 'bosses')->checkboxList(
            ArrayHelper::map($peopleWork, 'id', function ($person) {
                return $person->getFIO(PersonInterface::FIO_FULL);
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

    <br>
    <label><b>Выделите всех присутствовавших педагогов:</b></label><br>
    <div class="checkbox-list">
        <?= $form->field($model, 'teachers')->checkboxList(
            ArrayHelper::map($model->group->teachersWork, 'id', function (TeacherGroupWork  $groupWork) {
                return $groupWork->teacherWork->getFIO(PersonInterface::FIO_FULL);
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
    <br>
    <?php
        $AdditionalWork = PeopleWork::findAll(['id' => $model->ResponsiblePeople]);

    ?>


    <?= $form->field($model, "responsiblepeople")->widget(Select2::class, [
        'data' => ArrayHelper::map($AdditionalWork, 'id', function ($person) {
            return $person->getFIO(PersonInterface::FIO_FULL);
        }),
        'size' => Select2::LARGE,
        'options' => [
            'prompt' => 'Выберите ответственного' ,
            'multiple' => true
        ],
        'pluginOptions' => [
            'allowClear' => true
        ],
    ])->label('Выберите дополнительных людей, присутствовавших на защите'); ?>

    <br>
    <label><b>Выделите всех присутствовавших учеников на защите:</b></label><br>
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