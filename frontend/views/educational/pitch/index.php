<?php

use common\helpers\DateFormatter;
use common\helpers\StringFormatter;
use frontend\components\routes\Urls;
use frontend\models\work\educational\training_group\TrainingGroupWork;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $progress TrainingGroupWork[] */
/* @var $finished TrainingGroupWork[] */
?>

<div id="pitch-model">
    <h4>Прошедшие защиты</h4>
    <table class="table table-striped">
        <tr>
            <th>Номер группы</th>
            <th>Дата защиты</th>
            <th>Темы проектов</th>
        </tr>
        <?php foreach ($finished as $group): ?>
            <tr>
                <td>
                    <?= StringFormatter::stringAsLink($group->number, Url::to([Urls::TRAINING_GROUP_VIEW, 'id' => $group->id])) ?>
                </td>
                <td>
                    <?= DateFormatter::format($group->protection_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot) ?>
                </td>
                <td>
                    <?= $group->getThemesProjectPretty() ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>

    <h4>Предстоящие защиты</h4>
    <table class="table table-bordered">
        <tr>
            <th>Номер группы</th>
            <th>Дата защиты</th>
            <th>Тема проекта</th>
            <th>Описание проекта</th>
        </tr>
        <?php foreach ($progress as $group): ?>
            <?php foreach($group->groupProjectThemesWorks as $groupProjectThemesWork): ?>
                <tr>
                    <td>
                        <?= StringFormatter::stringAsLink($group->number, Url::to([Urls::TRAINING_GROUP_VIEW, 'id' => $group->id])) ?>
                    </td>
                    <td>
                        <?= DateFormatter::format($group->protection_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot) ?>
                    </td>
                    <td>
                        <?= $groupProjectThemesWork->projectThemeWork->name ?>
                    </td>
                    <td>
                        <?= $groupProjectThemesWork->projectThemeWork->description ?>
                    </td>
                    <td>
                        <?php if ($groupProjectThemesWork->confirm): ?>
                            <?= Html::a('Отклонить', Url::to(['/educational/pitch/decline-theme', 'id' => $groupProjectThemesWork->id]), ['class' => 'btn btn-danger']) ?>
                        <?php else: ?>
                            <?= Html::a('Подтвердить', Url::to(['/educational/pitch/confirm-theme', 'id' => $groupProjectThemesWork->id]), ['class' => 'btn btn-success']) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        <?php endforeach; ?>
    </table>
</div>

