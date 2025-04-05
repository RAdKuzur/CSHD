<?php

/** @var yii\web\View $this */

use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
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
                <?= StringFormatter::stringAsLink('Входящая документация', Url::to([Yii::$app->frontUrls::DOC_IN_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Исходящая документация', Url::to([Yii::$app->frontUrls::DOC_OUT_INDEX]))?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_DOCUMENT_FLOW); ?>
                Документооборот
            </div>
            <div>
                <?= StringFormatter::stringAsLink('Приказы по основной деятельности', Url::to([Yii::$app->frontUrls::ORDER_MAIN_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Приказы по учету достижений', Url::to([Yii::$app->frontUrls::ORDER_EVENT_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Приказы по учебной деятельности', Url::to([Yii::$app->frontUrls::ORDER_TRAINING_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Положения', Url::to([Yii::$app->frontUrls::REG_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Учет ответственности работников', Url::to([Yii::$app->frontUrls::LOCAL_RESPONSIBILITY_INDEX]))?>
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
                <?= StringFormatter::stringAsLink('Мероприятия', Url::to([Yii::$app->frontUrls::OUR_EVENT_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Учет достижений в мероприятиях', Url::to([Yii::$app->frontUrls::FOREIGN_EVENT_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Положение о мероприятиях', Url::to([Yii::$app->frontUrls::REG_EVENT_INDEX]))?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_EDUCATIONAL); ?>
                Учебная деятельность
            </div>
            <div>
                <?= StringFormatter::stringAsLink('Образовательные программы', Url::to([Yii::$app->frontUrls::PROGRAM_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Учебные группы', Url::to([Yii::$app->frontUrls::TRAINING_GROUP_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Генерация сертификатов', Url::to([Yii::$app->frontUrls::CERTIFICATE_INDEX]))?>
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
                <?= StringFormatter::stringAsLink('Ошибки заполнения', Url::to([Yii::$app->frontUrls::ANALITIC_ERRORS_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Защиты учебных групп', Url::to([Yii::$app->frontUrls::PITCH]))?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_DIRECTORY); ?>
                Справочники
            </div>
            <div>
                <?= StringFormatter::stringAsLink('Люди', Url::to([Yii::$app->frontUrls::PEOPLE_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Организации', Url::to([Yii::$app->frontUrls::COMPANY_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Должности', Url::to([Yii::$app->frontUrls::POSITION_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Участники деятельности', Url::to([Yii::$app->frontUrls::FOREIGN_EVENT_INDEX]))?>
                ,
                <?= StringFormatter::stringAsLink('Помещения', Url::to([Yii::$app->frontUrls::AUDITORIUM_INDEX]))?>
            </div>
        </div>
    </div>
</div>
