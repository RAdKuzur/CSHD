<?php

use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeoplePositionCompanyBranchWork;
use frontend\models\work\general\PeopleWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PeopleWork */
/* @var $companies CompanyWork */
/* @var $positions PositionWork */
/* @var $branches */
/* @var $modelPeoplePositionBranch PeoplePositionCompanyBranchWork */

$this->title = 'Редактировать человека: ' . $model->surname.' '.$model->firstname.' '.$model->patronymic;
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->surname.' '.$model->firstname.' '.$model->patronymic, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="people-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
        'companies' => $companies,
        'positions' => $positions,
        'branches' => $branches
    ]) ?>

</div>
