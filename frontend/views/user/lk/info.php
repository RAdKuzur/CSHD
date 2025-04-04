<?php

use common\models\work\UserWork;
use frontend\models\work\rubac\UserPermissionFunctionWork;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model UserWork */
/* @var $permissions */
//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu', ['model' => $model]) ?>

    <div class="content-container" style="float: left">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'surname',
                'firstname',
                'patronymic',
                'username',
            ],
        ]) ?>
        <?= GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $permissions,
                'pagination' => false,
            ]),
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                ['attribute' => 'name', 'label' => 'Разрешение']
            ],
        ]) ?>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>