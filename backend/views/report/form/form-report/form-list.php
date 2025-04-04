<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php
$this->title = 'Отчеты по готовым формам';
?>

<h3>Отчеты по готовым формам</h3>

<?= Html::a("Эффективный контракт", Url::to(['report-form/effective-contract']), ['class'=>'btn btn-success']); ?>

<?= Html::a("Отчет 1-ДОП", Url::to(['report-form/do-dop-1']), ['class'=>'btn btn-success']); ?>

<?= Html::a("Отчет ДО", Url::to(['report-form/do']), ['class'=>'btn btn-success']); ?>

<?= Html::a("Расчет выработки пед. работников", Url::to(['report-form/teacher']), ['class'=>'btn btn-success']); ?>

<?= Html::a("Отчет гос. задание", Url::to(['state-assignment']), ['class'=>'btn btn-primary']); ?>

<?= Html::a("Отчет ДОД", Url::to(['dod']), ['class'=>'btn btn-primary']); ?>
