<?php
/* @var $dataProvider */
/* @var $model */
/* @var $groupCheckOption */
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\grid\GridView;
?>
<div class = "training-group">
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'name' => 'group-selection',
            'checkboxOptions' => function (TrainingGroupWork $group) use ($model, $groupCheckOption) {
                if ($groupCheckOption == NULL) {
                    $condition = false;
                }
                else {
                    if (in_array($group->id, $groupCheckOption)){
                        $condition = true;
                    }
                    else {
                        $condition = false;
                    }
                }
                return [
                    'class' => 'group-checkbox',
                    'data-id' => $group->id,
                    //'checked' => $group->getActivity($model->id) == 1,
                    'checked' => $condition
                ];
            },
        ],
        'number',
        'start_date',
        'finish_date',
    ],
    'summaryOptions' => [
        'style' => 'display: none;',
    ],
]);
?>
</div>
