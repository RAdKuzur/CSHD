<?php

use frontend\forms\ResponsibilityForm;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ResponsibilityForm */
/* @var $audsList */
/* @var $peoples */
/* @var $orders */
/* @var $regulations */
/* @var $modelResponsibility LocalResponsibilityWork */

$this->title = 'Редактировать ответственность работника: ' .
    $modelResponsibility->peopleStampWork->surname . ' ' .
    Yii::$app->responsibilityType->get($modelResponsibility->responsibility_type);
$this->params['breadcrumbs'][] = ['label' => 'Учет ответственности работников', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' =>
        $modelResponsibility->peopleStampWork->surname . ' ' .
        Yii::$app->responsibilityType->get($modelResponsibility->responsibility_type),
    'url' => ['view', 'id' => $modelResponsibility->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<div class="local-responsibility-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'audsList' => $audsList,
        'peoples' => $peoples,
        'orders' => $orders,
        'regulations' => $regulations,
        'modelResponsibility' => $modelResponsibility,
    ]) ?>

</div>
