<?php

use app\components\VerticalActionColumn;
use common\helpers\html\HtmlCreator;
use frontend\models\search\SearchPeople;
use frontend\models\work\general\PeopleWork;
use kartik\export\ExportMenu;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $tematicDirectionList []string */


$this->title = 'Тематические направления';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-index">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?php
    $list = Yii::$app->thematicDirection->getFullnameList();
    ?>

    <div class="substrate">
        <ol>
        <?php foreach ($list as $item): ?>
            <div style="margin: 5px;">
                <li><?=  Html::encode($item)?></li>
            </div>
        <?php endforeach; ?>
        </ol>
    </div>

</div>