<?php

use yii\helpers\Html;

/* @var array $items */
/* @var string|null $placeholder */

?>
<div class="search-dropdown">
    <input type="text" class="select-search-input" placeholder="<?= Html::encode($placeholder) ?>" />
    <ul class="select-search-list">
        <?php foreach ($items as $item): ?>
            <li class="select-search-item"><?= Html::encode($item) ?></li>
        <?php endforeach; ?>
    </ul>
</div>