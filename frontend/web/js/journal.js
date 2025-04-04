/**
 * Изменение размеров окна электронного журнала
 * @param step
 */
function resize(step) {
    let table = document.getElementById("journal");

    if(table) {
        table.style.height = table.offsetHeight + step + "px";
    }
}

/**
 * "Конструктор" журнала
 */
document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('.journal-edit')) {
        applyStatusBlockToRowCells();
        init();
    }
});

/**
 * Глобальные переменные
 */
let currentIcon, IconTurnoutLink, IconNonAppearanceLink, IconDistantLink, IconDroppedLink, IconProjectLink, elements, elementsProject, svgData = '';
let IconTurnout, IconNonAppearance, IconDistant, IconDropped, IconProject, valueProject = '';

/**
 * Функция обновления данных по столбцам
 * @param header
 * @param columnIndex
 */
function clickOneCellThead(header, columnIndex)
{
    const table = header.closest('table');
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cell = row.cells[columnIndex];
        if (cell) {
            if (!cell.classList.contains('status-block')) {
                clickOneCell(cell);
            }
        }
    });
}

/**
 * Обертка-замыкание для передачи параметров
 * @param oneCell
 * @returns {(function(*): void)|*}
 */
const eventHandler = (oneCell) => {
    return function (event) {
        clickOneCell(oneCell);
    };
}

/**
 * Функция обновления данных новым статусом
 * @param oneCell
 */
function clickOneCell(oneCell)
{
    let statusValue = 3;

    switch (currentIcon) {
        case IconTurnoutLink:
            statusValue = 0;
            svgData = IconTurnout;
            break;
        case IconNonAppearanceLink:
            statusValue = 1;
            svgData = IconNonAppearance;
            break;
        case IconDistantLink:
            statusValue = 2;
            svgData = IconDistant;
            break;
        case IconDroppedLink:
            statusValue = 3;
            svgData = IconDropped;
            break;
        case IconProjectLink:
            statusValue = valueProject;
            break;
        default:
            return;
    }

    if (oneCell.classList.contains('project-participant')) {
        let selectInput = oneCell.getElementsByTagName('select')[0];
        selectInput.disabled = true;
        for (let i = 0; i < selectInput.options.length; i++) {
            selectInput.options[i].selected = false;
        }
        selectInput.options[statusValue].selected = true;
        selectInput.disabled = false;
    } else if (oneCell.classList.contains('status-participant') && currentIcon != IconProjectLink) {
        let oldSVG = oneCell.getElementsByTagName('svg');
        if (oldSVG.length > 0 && currentIcon) {
            oldSVG[0].remove();
        }
        oneCell.innerHTML += svgData;

        let statusCell = oneCell.getElementsByClassName('status')[0];
        statusCell.value = statusValue;
    }
}

/**
 * Сохранение загруженных svg в переменные
 * @param IconTurnoutLink
 * @param IconNonAppearanceLink
 * @param IconDistantLink
 * @param IconDroppedLink
 * @param IconProjectLink
 * @returns {Promise<void>}
 */
async function saveSvgFile(IconTurnoutLink, IconNonAppearanceLink, IconDistantLink, IconDroppedLink, IconProjectLink) {
    IconTurnout = await loadSvgFile(IconTurnoutLink);
    IconNonAppearance = await loadSvgFile(IconNonAppearanceLink);
    IconDistant = await loadSvgFile(IconDistantLink);
    IconDropped = await loadSvgFile(IconDroppedLink);
    IconProject = await loadSvgFile(IconProjectLink);
}

/**
 * Загрузка svg
 * @param filePath
 * @returns {Promise<null|string>}
 */
async function loadSvgFile(filePath) {
    try {
        const response = await fetch(filePath); // Загружаем файл
        if (!response.ok) {
            console.error(response);
        }
        return await response.text();
    } catch (error) {
        console.error(error.message);
        return null;
    }
}

/**
 * Функция для изменения иконки и сохранения её состояния
 * @param iconLink
 * @param event
 */
function changeCursorAndSaveIcon(iconLink, event) {
    let cursor = 'url('+iconLink+') 0 0, auto';
    if (iconLink === currentIcon) {
        cursor = 'default';
        currentIcon = '';
    } else {
        currentIcon = iconLink;
    }
    document.body.style.cursor = cursor;

    let changeElements = elements;
    if (currentIcon === IconProjectLink) {
        changeElements = elementsProject;
        valueProject = event.target.dataset.value;
    }

    Array.from(changeElements).forEach(element => {
        element.style.cursor = cursor;
    });
}

/**
 * Добавляет указание о состоянии блокировки к ячейкам строк
 */
function applyStatusBlockToRowCells() {
    const table = document.getElementById('journal-tbody');
    const rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        const firstCell = row.firstElementChild;
        const status = firstCell.getElementsByClassName('status-block');

        if (firstCell && status.length > 0) {
            Array.from(row.children).forEach(cell => {
                if (!cell.classList.contains('status-block')) {
                    cell.classList.add('status-block');
                    if (cell.classList.contains('project-participant')) {
                        cell.getElementsByTagName('select')[0].disabled = true;
                    }
                }
            });
        }
    });
}

/**
 * Возврат курсора и сброс статуса указателя мыши при клике вне целевого элемента
 */
document.addEventListener('DOMContentLoaded', function() {
    const targetElement = document.querySelector('#journal');
    const table = targetElement.getElementsByTagName('table')[0];
    const secondTargetElement = document.getElementsByClassName('control-unit')[0];

    document.body.addEventListener('click', function(event) {
        if (!table.contains(event.target) && !secondTargetElement.contains(event.target)) {
            currentIcon = '';
            document.body.style.cursor = 'default';
        }
    });
});