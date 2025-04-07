<?php

/** @var yii\web\View $this */

use common\helpers\ButtonsFormatter;
use common\helpers\files\FilePaths;
use common\helpers\html\HtmlBuilder;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'ЦСХД';
?>

<script>
    document.addEventListener("click", function(event) {
        if (event.target.dataset.action === 'flip-card') {
            const cardId = event.target.closest(".index-card").id;
            const cardElement = document.getElementById(cardId);
            cardElement.classList.toggle('flip');
        }
    });
</script>

<div class="site-index">
    <div class="block-card flexx space-around">
        <div class="index-card" id="card-1">
            <div class="front-card">
                <div class="index-title">
                    <?= HtmlBuilder::paintSVG(FilePaths::SVG_MAIL); ?>
                    Почта
                </div>
                <div style="text-align: center">
                    Входящая и исходящая документация<br>учреждения
                </div>
                <button data-action="flip-card" class="btn btn-success">⮕</button>
            </div>
            <div class="back-card">
                <div class="index-title flexx space">
                    <div>Почта</div>
                    <button data-action="flip-card" class="btn btn-success">х</button>
                </div>
                <div class="flexx">
                    <?php
                        $svg = HtmlBuilder::paintSVG(FilePaths::SVG_MAIL);
                        echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Входящая документация', Yii::$app->frontUrls::DOC_IN_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
                <div class="flexx">
                    <?php
                        $svg = HtmlBuilder::paintSVG(FilePaths::SVG_MAIL);
                        echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Исходящая документация', Yii::$app->frontUrls::DOC_OUT_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
            </div>
        </div>
        <div class="index-card"  id="card-2">
            <div class="front-card">
                <div class="index-title">
                    <?= HtmlBuilder::paintSVG(FilePaths::SVG_DOCUMENT_FLOW); ?>
                    Документооборот
                </div>
                <div style="text-align: center">
                    Приказы, положения и <br>учет ответственности работников
                </div>
                <button data-action="flip-card" class="btn btn-success">⮕</button>
            </div>
            <div class="back-card">
                <div class="index-title flexx space">
                    <div>Документооборот</div>
                    <button data-action="flip-card" class="btn btn-success">х</button>
                </div>
                <div>
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_DOCUMENT_FLOW);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Приказы по основной деятельности', Yii::$app->frontUrls::ORDER_MAIN_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Приказы по учету достижений', Yii::$app->frontUrls::ORDER_EVENT_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Приказы по учебной деятельности', Yii::$app->frontUrls::ORDER_TRAINING_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Положения', Yii::$app->frontUrls::REG_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Учет ответственности работников', Yii::$app->frontUrls::LOCAL_RESPONSIBILITY_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="block-card flexx space-around">
        <div class="index-card" id="card-3">
            <div class="front-card">
                <div class="index-title">
                    <?= HtmlBuilder::paintSVG(FilePaths::SVG_ACHIEVEMENT); ?>
                    Достижения
                </div>
                <div style="text-align: center">
                    Мероприятия и положения, <br>учёт достижений в мероприятиях
                </div>
                <button data-action="flip-card" class="btn btn-success">⮕</button>
            </div>
            <div class="back-card">
                <div class="index-title flexx space">
                    <div>Достижения</div>
                    <button data-action="flip-card" class="btn btn-success">х</button>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_ACHIEVEMENT);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Мероприятия', Yii::$app->frontUrls::OUR_EVENT_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_ACHIEVEMENT);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Учет достижений в мероприятиях', Yii::$app->frontUrls::FOREIGN_EVENT_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_ACHIEVEMENT);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Положение о мероприятиях', Yii::$app->frontUrls::REG_EVENT_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
            </div>
        </div>
        <div class="index-card" id="card-4">
            <div class="front-card">
                <div class="index-title">
                    <?= HtmlBuilder::paintSVG(FilePaths::SVG_EDUCATIONAL); ?>
                    Учебная деятельность
                </div>
                <div style="text-align: center">
                    Образовательные программы, учебные группы, <br>сертификаты
                </div>
                <button data-action="flip-card" class="btn btn-success">⮕</button>
            </div>
            <div class="back-card">
                <div class="index-title flexx space">
                    <div>Учебная деятельность</div>
                    <button data-action="flip-card" class="btn btn-success">х</button>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_EDUCATIONAL);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Образовательные программы', Yii::$app->frontUrls::PROGRAM_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_EDUCATIONAL);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Учебные группы', Yii::$app->frontUrls::TRAINING_GROUP_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_EDUCATIONAL);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Генерация сертификатов', Yii::$app->frontUrls::CERTIFICATE_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="block-card flexx space-around">
        <div class="index-card" id="card-5">
            <div class="front-card">
                <div class="index-title">
                    <?= HtmlBuilder::paintSVG(FilePaths::SVG_ANALYTIC); ?>
                    Аналитика
                </div>
                <div style="text-align: center">
                    Ошибки заполнения, защиты учебных групп
                </div>
                <button data-action="flip-card" class="btn btn-success">⮕</button>
            </div>
            <div class="back-card">
                <div class="index-title flexx space">
                    <div>Аналитика</div>
                    <button data-action="flip-card" class="btn btn-success">х</button>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_ANALYTIC);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Ошибки заполнения', Yii::$app->frontUrls::ANALITIC_ERRORS_INDEX, ButtonsFormatter::BTN_SUCCESS, '', ButtonsFormatter::createParameterLink(Yii::$app->rubac->authId())));
                    ?>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_ANALYTIC);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Защиты учебных групп', Yii::$app->frontUrls::PITCH, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
            </div>
        </div>
        <div class="index-card" id="card-6">
            <div class="front-card">
                <div class="index-title">
                    <?= HtmlBuilder::paintSVG(FilePaths::SVG_DIRECTORY); ?>
                    Справочники
                </div>
                <div style="text-align: center">
                    Люди, организации, должности, помещения,<br>участники деятельности
                </div>
                <button data-action="flip-card" class="btn btn-success">⮕</button>
            </div>
            <div class="back-card">
                <div class="index-title flexx space">
                    <div>Справочники</div>
                    <button data-action="flip-card" class="btn btn-success">х</button>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_DIRECTORY);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Люди', Yii::$app->frontUrls::PEOPLE_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Организации', Yii::$app->frontUrls::COMPANY_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_DIRECTORY);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Должности', Yii::$app->frontUrls::POSITION_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Помещения', Yii::$app->frontUrls::AUDITORIUM_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
                <div class="flexx">
                    <?php
                    $svg = HtmlBuilder::paintSVG(FilePaths::SVG_DIRECTORY);
                    echo HtmlBuilder::createGroupButton(ButtonsFormatter::anyOneLink($svg . ' Участники деятельности', Yii::$app->frontUrls::FOREIGN_EVENT_INDEX, ButtonsFormatter::BTN_SUCCESS));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
