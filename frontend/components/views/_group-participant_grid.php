<?php

use common\components\dictionaries\base\NomenclatureDictionary;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $model \frontend\models\work\order\OrderTrainingWork*/
/* @var $nomenclature */
/* @var $transferGroups */
/* @var $groupParticipantOption */
?>
<div class = "training-group-participant">
<?php
if ($dataProvider != NULL) {
    if (NomenclatureDictionary::getStatus($nomenclature) != NomenclatureDictionary::ORDER_TRANSFER) {
        // зачисление и отчисление
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'group-participant-selection',
                    'checkboxOptions' => function (TrainingGroupParticipantWork $participant) use ($model, $groupParticipantOption) {
                        if ($groupParticipantOption == NULL) {
                            $condition = false;
                        }
                        else {
                            if (in_array($participant->id, $groupParticipantOption)){
                                $condition = true;
                            }
                            else {
                                $condition = false;
                            }
                        }
                        return [
                            'class' => 'group-participant-checkbox',
                            'training-group-id' => $participant->training_group_id,
                            'data-id' => $participant->id, // Добавляем ID группы для передачи в JS
                            'checked' => $condition
                             //'checked' => $participant->getActivity($model->id) == 1
                            //'checked' => in_array($participant->id, $groupParticipantOption),
                             //'checked' => call_user_func_array([$participant, $groupParticipantOption[0]], $groupParticipantOption[1]) == 1
                        ];
                    },
                ],
                ['value' =>'fullFio', 'label' => 'ФИО обучающегося',],
                [
                    'attribute' => 'dropdownField', // Условное имя атрибута
                    'format' => 'raw', // Чтобы отобразить HTML-код
                    'label' => 'Группа',
                    'value' => function (TrainingGroupParticipantWork $participant) use ($transferGroups, $model) {
                        return $participant->trainingGroupWork->number;
                    },
                ],
            ],
            'rowOptions' => function ($model, $key, $index) {
                return ['id' => 'row-' . $model->id, 'class' => 'row-class-' . $index, 'name' => 'row-' . $model->training_group_id];
            },
            'tableOptions' => [
                'class' => 'table table-striped table-bordered',
                'style' => 'position: relative;', // Необязательно, для кастомизации таблицы
            ],
            'summaryOptions' => [
                'style' => 'display: none;', // Скрыть блок через CSS
            ],
        ]);
    } else {
        //перевод
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'name' => 'group-participant-selection',
                    'checkboxOptions' => function (TrainingGroupParticipantWork $participant) use ($model, $groupParticipantOption) {
                        if ($groupParticipantOption == NULL) {
                            $condition = false;
                        }
                        else {
                            if (in_array($participant->id, $groupParticipantOption)){
                                $condition = true;
                            }
                            else {
                                $condition = false;
                            }
                        }
                        return [
                            'class' => 'group-participant-checkbox',
                            'training-group-id' => $participant->training_group_id,
                            'data-id' => $participant->id, // Добавляем ID группы для передачи в JS
                            'checked' => $condition,
                            //'checked' => in_array($participant->id, $groupParticipantOption),

                            //'checked' => $participant->getActivity($participant->id, $model->id) == 1,
                            //'checked' => call_user_func_array([$participant, $groupParticipantOption[0]], $groupParticipantOption[1]) == 1
                        ];
                    },
                ],
                'fullFio',
                [
                    'attribute' => 'dropdownField', // Условное имя атрибута
                    'format' => 'raw', // Чтобы отобразить HTML-код
                    'label' => 'Исходная групп',
                    'value' => function (TrainingGroupParticipantWork $participant) use ($transferGroups, $model) {
                        return $participant->trainingGroupWork->number;
                    },
                ],
                [
                    'attribute' => 'dropdownField', // Условное имя атрибута
                    'format' => 'raw', // Чтобы отобразить HTML-код
                    'label' => 'Куда переводится',
                    'value' => function (TrainingGroupParticipantWork $participant) use ($transferGroups, $model) {
                        // Формируем HTML-код выпадающего списка
                        return Html::dropDownList(
                            'transfer-group[' . $participant->id . ']', // Имя элемента
                            $participant->getActualGroup($model->id), // Значение по умолчанию
                            ArrayHelper::map($transferGroups, 'id', 'number'),
                            [
                                'class' => 'form-control', // CSS-класс
                                'data-id' => $participant->id, // Пользовательские атрибуты
                            ]
                        );
                    },
                ],
            ],
            'rowOptions' => function ($model, $key, $index) {
                return ['id' => 'row-' . $model->id, 'class' => 'row-class-' . $index, 'name' => 'row-' . $model->training_group_id];
            },
            'tableOptions' => [
                'class' => 'table table-striped table-bordered',
                'style' => 'position: relative;', // Необязательно, для кастомизации таблицы
            ],
            'summaryOptions' => [
                'style' => 'display: none;', // Скрыть блок через CSS
            ],
        ]);
    }
}
?>
</div>
