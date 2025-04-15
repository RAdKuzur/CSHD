<?php

use common\components\dictionaries\base\BranchDictionary;
use frontend\models\work\dictionaries\CompanyWork;
use frontend\models\work\dictionaries\PositionWork;
use frontend\models\work\general\PeopleWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model PeopleWork */
/* @var $companies CompanyWork */
/* @var $positions PositionWork */
/* @var $branches */

/* @var $branches */

$this->title = 'Добавить человека';
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="people-create">

    <div class="substrate">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'companies' => $companies,
        'positions' => $positions,
        'branches' => $branches,
    ]) ?>

</div>
