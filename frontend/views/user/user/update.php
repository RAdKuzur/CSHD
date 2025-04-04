<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\UserWork
 * @var $modelRole \app\models\work\RoleWork
 */

$this->title = 'Редактировать пользователя: ' . $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fullName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelRole' => $modelRole,
    ]) ?>

</div>
