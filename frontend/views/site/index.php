<?php

/** @var yii\web\View $this */

use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'ЦСХД';
?>

<div class="site-index">
    <div class="block-card flexx space-around">
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_MAIL); ?>
                Почта
            </div>
            <div>
                <?= Html::a('Входящая документация', Url::to([Yii::$app->frontUrls::DOC_IN_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Исходящая документация', Url::to([Yii::$app->frontUrls::DOC_OUT_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_DOCUMENT_FLOW); ?>
                Документооборот
            </div>
            <div>
                <?= Html::a('Приказы по основной деятельности', Url::to([Yii::$app->frontUrls::ORDER_MAIN_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Приказы по учету достижений', Url::to([Yii::$app->frontUrls::ORDER_EVENT_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Приказы по учебной деятельности', Url::to([Yii::$app->frontUrls::ORDER_TRAINING_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Положения', Url::to([Yii::$app->frontUrls::REG_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Учет ответственности работников', Url::to([Yii::$app->frontUrls::LOCAL_RESPONSIBILITY_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
            </div>
        </div>
    </div>
    <div class="block-card flexx space-around">
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_ACHIEVEMENT); ?>
                Достижения
            </div>
            <div>
                <?= Html::a('Мероприятия', Url::to([Yii::$app->frontUrls::OUR_EVENT_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Учет достижений в мероприятиях', Url::to([Yii::$app->frontUrls::FOREIGN_EVENT_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Положение о мероприятиях', Url::to([Yii::$app->frontUrls::REG_EVENT_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_EDUCATIONAL); ?>
                Учебная деятельность
            </div>
            <div>
                <?= Html::a('Образовательные программы', Url::to([Yii::$app->frontUrls::PROGRAM_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Учебные группы', Url::to([Yii::$app->frontUrls::TRAINING_GROUP_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Генерация сертификатов', Url::to([Yii::$app->frontUrls::CERTIFICATE_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
            </div>
        </div>
    </div>
    <div class="block-card flexx space-around">
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_ANALYTIC); ?>
                Аналитика
            </div>
            <div>
                <?= Html::a('Ошибки заполнения', Url::to([Yii::$app->frontUrls::ANALITIC_ERRORS_INDEX, 'id' => Yii::$app->rubac->authId()]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Защиты учебных групп', Url::to([Yii::$app->frontUrls::PITCH]), ['class' => 'badge bg-secondary index-badge'])?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_DIRECTORY); ?>
                Справочники
            </div>
            <div>
                <?= Html::a('Люди', Url::to([Yii::$app->frontUrls::PEOPLE_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Организации', Url::to([Yii::$app->frontUrls::COMPANY_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Должности', Url::to([Yii::$app->frontUrls::POSITION_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Участники деятельности', Url::to([Yii::$app->frontUrls::FOREIGN_EVENT_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
                <?= Html::a('Помещения', Url::to([Yii::$app->frontUrls::AUDITORIUM_INDEX]), ['class' => 'badge bg-secondary index-badge'])?>
            </div>
        </div>
    </div>
</div>
