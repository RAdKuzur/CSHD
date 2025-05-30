<?php


/* @var $this yii\web\View */
/* @var $dataProviderGroup */
/* @var $model */
/* @var $dataProviderParticipant */
/* @var $nomenclature */
/* @var $transferGroups */
/* @var $groupCheckOption */
/* @var $groupParticipantOption */
?>

    <!-- Поиск по группам -->
    <div class="form-group">
        <input type="text" id="group-search" class="form-control"
               placeholder="Поиск по группам..."
               style="margin-bottom: 15px;">
    </div>


    <?= $this->render('_groups_grid', [
        'dataProvider' => $dataProviderGroup,
        'model' => $model,
        'groupCheckOption' => $groupCheckOption,
    ]);
    ?>


    <!-- Поиск по участникам -->
    <div class="form-group">
        <input type="text" id="participant-search" class="form-control"
               placeholder="Поиск по участникам..."
               style="margin-bottom: 15px;margin-top: 15px;">
    </div>

    <?= $this->render('_group-participant_grid', [
        'dataProvider' => $dataProviderParticipant,
        'model' => $model,
        'nomenclature' => $nomenclature,
        'transferGroups' => $transferGroups,
        'groupParticipantOption' => $groupParticipantOption,
    ]);
    ?>
