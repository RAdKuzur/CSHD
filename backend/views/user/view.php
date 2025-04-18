<?php

use backend\models\forms\UserForm;
use common\models\work\UserWork;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model UserForm */

$this->title = $model->entity->getFullName();
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->entity->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->entity->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить этого пользователя?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h4><u>Общая информация</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'entity.surname', 'label' => 'Фамилия'],
            ['attribute' => 'entity.firstname', 'label' => 'Имя'],
            ['attribute' => 'entity.patronymic', 'label' => 'Отчество'],
            ['attribute' => 'entity.username', 'label' => 'Логин'],
            ['attribute' => 'aka', 'label' => 'Также является', 'format' => 'raw', 'value' => function(UserForm $model) {
                return $model->getAkaLink();
            }],
            ['attribute' => 'rules', 'label' => 'Разрешения', 'format' => 'raw', 'value' => function(UserForm $model) {
                return implode('<br>', $model->getPermissions());
            }],
        ],
    ]) ?>
    <!--
    <h4><u>Административные права</u></h4>
    <?php /*DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'addUsers', 'value' => function($model) {if ($model->addUsers == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewRoles', 'value' => function($model) {if ($model->viewRoles == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editRoles', 'value' => function($model) {if ($model->editRoles == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'report', 'value' => function($model) {if ($model->report == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
        ],
    ]) ?>
    <h4><u>Права доступа к системе документооборота</u></h4>
    <?= /*DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'viewOut', 'value' => function($model) {if ($model->viewOut == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editOut', 'value' => function($model) {if ($model->editOut == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewIn', 'value' => function($model) {if ($model->viewIn == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editIn', 'value' => function($model) {if ($model->editIn == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewOrder', 'value' => function($model) {if ($model->viewOrder == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editOrder', 'value' => function($model) {if ($model->editOrder == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewRegulation', 'value' => function($model) {if ($model->viewRegulation == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editRegulation', 'value' => function($model) {if ($model->editRegulation == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewEvent', 'value' => function($model) {if ($model->viewEvent == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editEvent', 'value' => function($model) {if ($model->editEvent == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewForeign', 'value' => function($model) {if ($model->viewForeign == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editForeign', 'value' => function($model) {if ($model->editForeign == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewProgram', 'value' => function($model) {if ($model->viewProgram == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editProgram', 'value' => function($model) {if ($model->editProgram == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewAdd', 'value' => function($model) {if ($model->viewAdd == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editAdd', 'value' => function($model) {if ($model->editAdd == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
        ],
    ]) ?>
    <h4><u>Права доступа к реестру ПО</u></h4>
    <?= /*DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'viewAS', 'value' => function($model) {if ($model->viewAS == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editAS', 'value' => function($model) {if ($model->editAS == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
        ],
    ]) ?>
    <h4><u>Права доступа к электронному журналу</u></h4>
    <?= /*DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'viewGroup', 'value' => function($model) {if ($model->viewGroup == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editGroup', 'value' => function($model) {if ($model->editGroup == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'viewGroupBranch', 'value' => function($model) {if ($model->viewGroupBranch == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'editGroupBranch', 'value' => function($model) {if ($model->editGroupBranch == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'addGroup', 'value' => function($model) {if ($model->addGroup == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
            ['attribute' => 'deleteGroup', 'value' => function($model) {if ($model->deleteGroup == 1) return '<span class="badge badge-success">Да</span>';
            else return '<span class="badge badge-error">Нет</span>';}, 'format' => 'html'],
        ],
    ]) ?>
    */
    ?>
--></div>
