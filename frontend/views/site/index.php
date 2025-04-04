<?php

/** @var yii\web\View $this */

use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;

$this->title = 'My Yii Application';
?>

<div class="site-index">
    <div class="block-card flexx space-around">
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_MAIL); ?>
                Почта
            </div>
            <div>
                <?= StringFormatter::stringAsLink('Входящая документация', Yii::$app->frontUrls::DOC_IN_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Исходящая документация', Yii::$app->frontUrls::DOC_OUT_INDEX)?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_DOCUMENT_FLOW); ?>
                Документооборот
            </div>
            <div>
                <?= StringFormatter::stringAsLink('Приказы по основной деятельности', Yii::$app->frontUrls::ORDER_MAIN_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Приказы по учету достижений', Yii::$app->frontUrls::ORDER_EVENT_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Приказы по учебной деятельности', Yii::$app->frontUrls::ORDER_TRAINING_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Положения', Yii::$app->frontUrls::REG_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Учет ответственности работников', Yii::$app->frontUrls::LOCAL_RESPONSIBILITY_INDEX)?>
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
                <?= StringFormatter::stringAsLink('Мероприятия', Yii::$app->frontUrls::OUR_EVENT_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Учет достижений в мероприятиях', Yii::$app->frontUrls::FOREIGN_EVENT_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Положение о мероприятиях', Yii::$app->frontUrls::REG_EVENT_INDEX)?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_EDUCATIONAL); ?>
                Учебная деятельность
            </div>
            <div>
                <?= StringFormatter::stringAsLink('Образовательные программы', Yii::$app->frontUrls::PROGRAM_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Учебные группы', Yii::$app->frontUrls::TRAINING_GROUP_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Генерация сертификатов', Yii::$app->frontUrls::CERTIFICATE_INDEX)?>
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
                <?= StringFormatter::stringAsLink('Ошибки заполнения', Yii::$app->frontUrls::ANALITIC_ERRORS_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Защиты учебных групп', Yii::$app->frontUrls::PITCH)?>
            </div>
        </div>
        <div class="index-card">
            <div class="index-title">
                <?= HtmlBuilder::paintSVG(FilePaths::SVG_DIRECTORY); ?>
                Справочники
            </div>
            <div>
                <?= StringFormatter::stringAsLink('Люди', Yii::$app->frontUrls::PEOPLE_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Организации', Yii::$app->frontUrls::COMPANY_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Должности', Yii::$app->frontUrls::POSITION_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Участники деятельности', Yii::$app->frontUrls::FOREIGN_EVENT_INDEX)?>
                ,
                <?= StringFormatter::stringAsLink('Помещения', Yii::$app->frontUrls::AUDITORIUM_INDEX)?>
            </div>
        </div>
    </div>
</div>
