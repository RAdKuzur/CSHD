<?php

use common\components\wizards\AlertMessageWizard;
use common\models\scaffold\TrainingGroup;
use frontend\forms\training_group\TrainingGroupParticipantForm;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use kartik\select2\Select2Asset;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model TrainingGroupParticipantForm */
/* @var $modelChilds array */
/* @var $childs ForeignEventParticipantsWork[] */
/* @var $buttonsAct string */

$this->title = 'Редактирование ' . $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "Группа {$model->number}", 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;

// Подключаем JS для блокировки активности
$this->registerJsFile('@web/js/activity-locker.js', ['depends' => [\yii\web\JqueryAsset::class]]);
// Подключаем Select2 assets
Select2Asset::register($this);
?>

<div class="group-create">

    <?= AlertMessageWizard::showRedisConnectMessage() ?>

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct ?>
            </div>
        </div>
    </div>

    <div class="training-group-participant-form field-backing">
        <?php if (strlen($model->participantsTable) > 10): ?>
            <?= $model->participantsTable ?>
        <?php endif; ?>

        <?php $form = ActiveForm::begin(['id' => 'participant-form']); ?>

        <?= $form->field($model, 'participantFile')->fileInput(['multiple' => false])->label('Загрузить учащихся из файла') ?>

        <div class="bordered-div">
            <!-- Блок выбора -->
            <div class="panel-title"><h5>Добавить учащегося</h5></div>
            <div class="flexx" style="flex-wrap: wrap;">
                <div class="flx1 mt-3 ml-3 mr-3">
                    <div class="form-group field-participant_id_temp">
                        <?= Select2::widget([
                            'name' => 'participant_id_temp',
                            'id' => 'participant-select',
                            'data' => ArrayHelper::map($childs, 'id', fn($m) => $m->getFullFioWithBirthdate()),
                            'options' => [
                                'prompt' => 'Выберите ученика',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                            'size' => Select2::LARGE,
                        ]) ?>
                    </div>
                </div>
                <div class="flx1 mt-3 ml-3 mr-3">
                    <div class="form-group field-send_method_temp">
                        <?= Html::dropDownList('send_method_temp', null, Yii::$app->sendMethods->getList(), [
                            'class' => 'form-control',
                            'id' => 'send-method-select',
                            'prompt' => 'Способ доставки сертификата'
                        ]) ?>
                    </div>
                </div>
            </div>
            <!-- Кнопка в новой строке -->
            <div style="text-align: center; margin-bottom: 15px;">
                <button type="button" id="add-to-list" class="btn btn-success">
                    <span class="glyphicon glyphicon-plus"></span> Добавить
                </button>
            </div>

        </div>
            <!-- Таблица -->
            <div class="panel-title"><h5>Выбранные учащиеся</h5></div>
            <table class="table table-bordered" id="participant-table">
                <thead>
                <tr><th>ФИО</th><th>Способ доставки</th><th>Действие</th></tr>
                </thead>
                <tbody></tbody>
            </table>

            <!-- Скрытые поля для формы -->
            <div id="form-hidden-container"></div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<!-- Скрипт управления добавлением -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Script loaded, jQuery exists:', typeof jQuery !== 'undefined');
        console.log('Found add button:', document.getElementById('add-to-list'));

        // Инициализация Select2
        $('.select2').select2({ allowClear: true });

        let index = 0;
        const addBtn = document.getElementById('add-to-list');
        const tableBody = document.querySelector('#participant-table tbody');
        const hiddenContainer = document.getElementById('form-hidden-container');

        addBtn.addEventListener('click', function() {
            console.log('Add button clicked');
            const participantSelect = document.getElementById('participant-select');
            const methodSelect = document.getElementById('send-method-select');
            const participantId = participantSelect.value;
            const participantText = participantSelect.options[participantSelect.selectedIndex]?.text;
            const sendMethod = methodSelect.value;
            const sendMethodText = methodSelect.options[methodSelect.selectedIndex]?.text;

            if (!participantId || !sendMethod) {
                alert('Выберите и участника, и способ отправки');
                return;
            }

            // Добавляем строку
            const row = document.createElement('tr');
            row.setAttribute('data-index', index);
            row.innerHTML = `
                <td>${participantText}</td>
                <td>${sendMethodText}</td>
                <td><button type="button" class="btn btn-danger btn-xs remove-row">Удалить</button></td>
            `;
            tableBody.appendChild(row);

            // если требуется id (даже пустой), добавьте:
            const inputId = document.createElement('input');
            inputId.type  = 'hidden';
            inputId.name  = `TrainingGroupParticipantWork[${index}][id]`;
            inputId.value = '';
            hiddenContainer.appendChild(inputId);

            // Добавляем скрытые поля
            const input1 = document.createElement('input');
            input1.type = 'hidden';
            input1.name = `TrainingGroupParticipantWork[${index}][participant_id]`;
            input1.value = participantId;
            const input2 = document.createElement('input');
            input2.type = 'hidden';
            input2.name = `TrainingGroupParticipantWork[${index}][send_method]`;
            input2.value = sendMethod;
            hiddenContainer.appendChild(input1);
            hiddenContainer.appendChild(input2);

            // Сброс
            participantSelect.value = '';
            $(participantSelect).trigger('change');
            methodSelect.value = '';

            index++;
        });

        // Делегирование удаления
        tableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                const tr = e.target.closest('tr');
                const idx = tr.getAttribute('data-index');
                tr.remove();

                // Удаляем скрытые поля по индексу
                const fields = ['participant_id', 'send_method', 'id'];
                fields.forEach(field => {
                    const selector = `input[name="TrainingGroupParticipantWork[${idx}][${field}]"]`;
                    document.querySelectorAll(selector).forEach(el => el.remove());
                });
            }
        });


        // Инициализация блокировок
        initObjectData(<?= $model->id ?>, '<?= TrainingGroup::tableName() ?>', '<?= Url::to(['educational/training-group/view', 'id' => $model->id]) ?>');
        setInterval(refreshLock, 600000);
    });
</script>