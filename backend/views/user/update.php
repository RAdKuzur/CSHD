<?php

use backend\models\forms\UserForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model UserForm */

$this->title = 'Редактировать пользователя: ' . $model->entity->getFullName();
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->entity->getFullName(), 'url' => ['view', 'id' => $model->entity->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
