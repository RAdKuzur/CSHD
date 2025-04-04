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

<?= $this->render('_groups_grid', [
    'dataProvider' => $dataProviderGroup,
    'model' => $model,
    'groupCheckOption' => $groupCheckOption,
]);
?>
<?= $this->render('_group-participant_grid', [
    'dataProvider' => $dataProviderParticipant,
    'model' => $model,
    'nomenclature' => $nomenclature,
    'transferGroups' => $transferGroups,
    'groupParticipantOption' => $groupParticipantOption,
]);
?>