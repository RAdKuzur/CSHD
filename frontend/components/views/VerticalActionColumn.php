<?php
use app\components\VerticalActionColumn;

echo \yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        // Другие колонки
        [
            'class' => VerticalActionColumn::class,
            'template' => '{view} {update} {delete}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('Просмотреть', $url, ['class' => 'btn btn-info']);
                },
                'update' => function ($url, $model, $key) {
                    return Html::a('Изменить', $url, ['class' => 'btn btn-primary']);
                },
                'delete' => function ($url, $model, $key) {
                    return Html::a('Удалить', $url, [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                            'method' => 'post',
                        ],
                    ]);
                },
            ],
        ],
    ],
]);
