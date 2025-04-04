<style>
    .bordered-div {
        border: 2px solid #000; /* Черная рамка */
        padding: 10px;          /* Отступы внутри рамки */
        border-radius: 5px;    /* Скругленные углы (по желанию) */
        margin: 10px 0;        /* Отступы сверху и снизу */
    }
</style>

<div class="bordered-div">
    <div> Ответственные </div>
    <button type="button" class="add-dropdown-resp">+</button>
    <div id="dropdown-container-resp">
        <div class="dropdown-group-resp" id="dropdown-template-resp" style="display:none;">
            <div class="bordered-div">
                <div> Ответственный </div>
                <select name="respPeople[]">
                    <option value="">Выберите</option>
                    <?php foreach ($bringPeople as $person): ?>
                        <option value="<?= htmlspecialchars($person->id) ?>">
                            <?= htmlspecialchars($person->getFullFio()) ?>
                        </option>
                    <?php endforeach;
                    ?>
                </select>
                <button type="button" class="remove-dropdown-resp">-</button>
            </div>
        </div>
    </div>
</div>
