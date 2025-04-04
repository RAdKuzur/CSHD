<?php

use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\StringFormatter;
use frontend\models\work\dictionaries\PersonInterface;
use frontend\models\work\general\PeopleWork;
use frontend\models\work\responsibility\LegacyResponsibleWork;
use frontend\models\work\responsibility\LocalResponsibilityWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model LocalResponsibilityWork */
/* @var $history LegacyResponsibleWork */
/* @var $buttonsAct */

$this->title = $model->peopleStampWork->surname . ' ' . Yii::$app->responsibilityType->get($model->responsibility_type);
if ($model->quant !== null) {
    $this->title .= ' №' . $model->quant;
}
$this->params['breadcrumbs'][] = ['label' => 'Учет ответственности работников', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="local-responsibility-view">

    <div class="substrate">
        <h1><?= Html::encode($this->title) ?></h1>

        <div class="flexx space">
            <div class="flexx">
                <?= $buttonsAct ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Вид
                    </div>
                    <div class="field-date">
                        <?= Yii::$app->responsibilityType->get($model->responsibility_type) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Работник
                    </div>
                    <div class="field-date">
                        <?= StringFormatter::stringAsLink(
                                $model->peopleStampWork->peopleWork->getFIO(PersonInterface::FIO_FULL),
                                Url::to([Yii::$app->frontUrls::PEOPLE_VIEW, 'id' => $model->peopleStampWork->people_id])
                            )
                        ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Идентификационные данные</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Отдел
                    </div>
                    <div class="field-date">
                        <?= Yii::$app->branches->get($model->branch) ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Помещение
                    </div>
                    <div class="field-date">
                        <?= StringFormatter::stringAsLink(
                                $model->auditoriumWork->name,
                                Url::to([Yii::$app->frontUrls::AUDITORIUM_VIEW, 'id' => $model->auditorium_id])
                            )
                        ?>
                    </div>
                </div>
                <?php if (!is_null($model->quant)): ?>
                    <div class="card-field flexx">
                        <div class="field-title">
                            Квант
                        </div>
                        <div class="field-date">
                            <?= $model->getRealDate() . ' № ' . $model->getRealNumber() ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Документы</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Приказ
                    </div>
                    <div class="field-date">
                        <?= $model->getCurrentOrder() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Положение/инструкция
                    </div>
                    <div class="field-date">
                        <?= $model->regulationWork ?
                            StringFormatter::stringAsLink(
                                $model->regulationWork->name,
                                Url::to([Yii::$app->frontUrls::REG_VIEW, 'id' => $model->regulation_id])
                            ) :
                            '---'
                        ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx files-section space-around">
                    <div class="file-block-center"><?php /*= $model->getFullScan();*/ ?><div>Файлы</div></div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">История ответственности</div>
                <div class="flexx">
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            История
                        </div>
                        <div class="field-date">
                            <?= $model->getLegacy() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
