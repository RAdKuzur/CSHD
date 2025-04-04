<style>
    .bordered-div {
        border: 2px solid #000; /* Черная рамка */
        padding: 10px;          /* Отступы внутри рамки */
        border-radius: 5px;    /* Скругленные углы (по желанию) */
        margin: 10px 0;        /* Отступы сверху и снизу */
    }
</style>
<div class="bordered-div">
    <div> Должность </div>
    <button type="button" class="add-dropdown-pos">+</button>
    <div id="dropdown-container-pos">
        <div class="dropdown-group-pos" id="dropdown-template-pos" style="display:none;">
            <div class="bordered-div">
                <div> Должность </div>
                <select name="pos[]">
                    <option value="">Выберите</option>
                    <?php foreach ($positions as $position): ?>
                        <option value="<?= htmlspecialchars($position->id) ?>">
                            <?= htmlspecialchars($position->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div> Отдел (при наличии) </div>
                <select name="side[]">
                    <option value=""> --- </option>
                    <?php foreach ($branches as $branch):  ?>
                        <option value="<?= htmlspecialchars($branch) ?>">
                            <?= htmlspecialchars($branch) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="button" class="remove-dropdown-pos">-</button>
            </div>
        </div>
    </div>
</div>
