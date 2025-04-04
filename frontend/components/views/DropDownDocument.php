<style>
    .bordered-div {
        border: 2px solid #000; /* Черная рамка */
        padding: 10px;          /* Отступы внутри рамки */
        border-radius: 5px;    /* Скругленные углы (по желанию) */
        margin: 10px 0;        /* Отступы сверху и снизу */
    }
</style>
<div class="bordered-div">
    <div> Изменение документов </div>
    <button type="button" class="add-dropdown-doc-ch">+</button>
    <div id="dropdown-container-doc-ch">
        <div class="dropdown-group-doc-ch" id="dropdown-template-doc-ch" style="display:none;">
            <div class="bordered-div">
                <div> Приказ </div>
                <select name="doc-1[]">
                    <option value="">Выберите</option>
                    <?php foreach ($bringPeople as $person): ?>
                        <option value="<?= htmlspecialchars($person->id) ?>">
                            <?= htmlspecialchars($person->getFullFio()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div> Положение </div>
                <select name="doc-2[]">
                    <option value="">Выберите</option>
                    <?php foreach ($bringPeople as $person): ?>
                        <option value="<?= htmlspecialchars($person->id) ?>">
                            <?= htmlspecialchars($person->getFullFio()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>
                    <input type="radio" name="radio[0]" value="cancel"> Отмена
                </label><br>
                <label>
                    <input type="radio" name="radio[0]" value="change"> Изменение
                </label><br>
                <button type="button" class="remove-dropdown-doc-ch">-</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let idCounter = 0; // Счетчик для уникальных ID

        // Функция для добавления нового блока
        document.querySelector(".add-dropdown-doc-ch").addEventListener("click", function () {
            idCounter++; // Увеличиваем счетчик

            // Создаем новый блок на основе шаблона
            const newDropdown = document.getElementById("dropdown-template-doc-ch").cloneNode(true);
            newDropdown.style.display = "block"; // Показываем новый блок
            newDropdown.id = ""; // Удаляем ID у клона, чтобы не было дубликатов

            // Обновляем имена радио-кнопок
            const radios = newDropdown.querySelectorAll('input[type="radio"]');
            radios.forEach(function (radio) {
            radio.name = `radio[${idCounter}]`;
            radio.id = idCounter;// Задаем уникальное имя
        });
    // Добавляем новый блок в контейнер
        document.getElementById("dropdown-container-doc-ch").appendChild(newDropdown);
    });

    document.addEventListener("click", function (event) {
        if (event.target.classList.contains("remove-dropdown-doc-ch")) {
            const dropdownGroup = event.target.closest(".dropdown-group-doc-ch");
            dropdownGroup.remove(); // Удаляем блок
            }
        });
    });
</script>