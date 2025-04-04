<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use frontend\models\search\SearchForeignEventParticipants;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel SearchForeignEventParticipants */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $buttonsAct */

$this->title = 'Участники деятельности';
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="foreign-event-participants-index">

        <div class="substrate">
            <h1><?= Html::encode($this->title) ?></h1>

            <div class="flexx space">
                <div class="flexx">
                    <?= $buttonsAct; ?>

                    <div class="export-menu">
                        <?php

                        $gridColumns = [
                            'surname',
                            'firstname',
                            'patronymic',
                            ['attribute' => 'sex', 'value' => function(ForeignEventParticipantsWork $model) {
                                return $model->getSexString();
                            }],
                            ['attribute' => 'birthdate', 'value' => function($model){return date("d.m.Y", strtotime($model->birthdate));}],
                            ['attribute' => 'eventsExcel', 'label' => 'Мероприятия', 'format' => 'raw'],
                            ['attribute' => 'studiesExcel', 'label' => 'Учебные группы'],
                        ];

                        echo ExportMenu::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => $gridColumns,

                            'options' => [
                                'padding-bottom: 100px',
                            ],
                        ]);

                        ?>
                    </div>
                </div>

                <?= HtmlCreator::filterToggle() ?>
            </div>
        </div>

        <?= $this->render('_search', ['model' => $searchModel]); ?>

        <?php
        echo '<div style="margin-bottom: 10px">'.Html::a('Показать участников с некорректными данными', \yii\helpers\Url::to(['foreign-event-participants/index', 'sort' => '1']), ['class' => 'btn btn-danger', 'style' => 'margin-right: 5px;']);
        echo Html::a('Показать участников с ограничениями на разглашение ПД', \yii\helpers\Url::to(['foreign-event-participants/index', 'sort' => '2']), ['class' => 'btn btn-info']).'</div>';
        ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'summary' => false,
            'columns' => [
                'surname',
                'firstname',
                'patronymic',
                ['attribute' => 'sex', 'value' => function(ForeignEventParticipantsWork $model) {
                    return $model->getSexString();
                }],
                ['attribute' => 'birthdate', 'value' => function($model){return date("d.m.Y", strtotime($model->birthdate));}],

                ['class' => VerticalActionColumn::class],
            ],
            'rowOptions' => function ($model) {
                return ['data-href' => Url::to([Yii::$app->frontUrls::PARTICIPANT_VIEW, 'id' => $model->id])];
            },
        ]); ?>
        <div class="form-group">
            <?= Html::a("Слияние участников деятельности", Url::to(['dictionaries/foreign-event-participants/merge-participant']), ['class'=>'btn btn-success']); ?>
        </div>

    </div>

<?php
$this->registerJs(<<<JS
            let totalPages = "{$dataProvider->pagination->pageCount}"; 
        JS, $this::POS_HEAD);
?>