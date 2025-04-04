/**
 * Функции для обновления блокировки ресурса по активности пользователя
 *
 * refreshLock - выполняет непосредственно обновление времени блокировки
 * setActive - проверяет внутренний таймер и вызывает refreshLock по его истечении
 */

let isActive = true;
let tObjectId, tObjectType, tBackUrl;


function initObjectData(objectId, objectType, backUrl)
{
    tObjectId = objectId;
    tObjectType = objectType;
    tBackUrl = backUrl;
}

function refreshLock(objectId, objectType)
{
    if (isActive) {
        isActive = false;
        // Записываем данные для снятия блокировки по триггеру закрытия страницы

        // Отправляем запрос на сервер для обновления блокировки
        $.ajax({
            url: 'index.php?r=utility/refresh-lock',
            type: 'POST',
            data: {
                objectId: tObjectId,
                objectType: tObjectType,
            },
            success: function(response) {

            },
            error: function(xhr, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                // Обработка ошибок
            }
        });

    }
    else {
        // снимаем блокировку ресурса и перенаправляем пользователя обратно
        $.ajax({
            url: 'index.php?r=utility/unlock',
            type: 'POST',
            data: {
                objectId: tObjectId,
                objectType: tObjectType,
            },
            success: function(response) {
                window.location.href = tBackUrl;
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                // Обработка ошибок
            }
        });
    }

}

// Обработчики событий для отслеживания активности
function setActive()
{
    isActive = true;
}

// Обработчики для отслеживания активности
window.addEventListener('mousedown', setActive);
window.addEventListener('keydown', setActive);

// Завершение редактирования, когда пользователь закрывает страницу или переключается
window.addEventListener('beforeunload', function() {
        $.ajax({
            url: 'index.php?r=utility/unlock&type=0',
            type: 'POST',
            data: {
                objectId: tObjectId,
                objectType: tObjectType,
            },
            success: function(response) {},
            error: function(xhr, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                // Обработка ошибок
            }
        });
});