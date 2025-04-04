/**
 * Поиск названия класса в теге
 * @param element
 * @returns {null|string}
 */
function getClassNameFromTag(element) {
    const classNamesToFind = ['desc', 'asc', 'no-sort', 'back', 'front'];

    for (const className of classNamesToFind) {
        if (element.classList.contains(className)) {
            return className;
        }
    }

    return null;
}

/**
 * Формирование SVG иконки для таблиц и сортировке по таблице
 */
class IconToggle {
    constructor(link) {
        this.link = link;
        this.icons = {
            'desc': this.getIcon(16, 16,'sort desc', '#009580','M12 23.247V.747M8.25 19.497l3.75 3.75 3.75-3.75'),
            'asc': this.getIcon(16, 16,'sort asc', '#009580','M12 23.247V.747M8.25 4.497L12 .747l3.75 3.75'),
            'no-sort': this.getIcon(12,16, 'no sort', '#000','M9 10.5v12.75M12 20.25l-3 3-3-3M15 13.5V.75M12 3.75l3-3 3 3'),
            'back': this.getIcon(16, 16, 'back', '#000','M15.848 16.836a1.07 1.07 0 0 1 .078 1.507 1.062 1.062 0 0 1-1.504.078l-6.27-5.672a1.07 1.07 0 0 1 .002-1.587l6.216-5.583a1.063 1.063 0 0 1 1.504.083 1.07 1.07 0 0 1-.083 1.507l-4.944 4.44a.473.473 0 0 0 0 .703l5.002 4.524Z'),
            'front' : this.getIcon(16, 16, 'front', '#000', 'm8.152 16.836 5.002-4.524a.475.475 0 0 0-.001-.703l-4.944-4.44a1.07 1.07 0 0 1-.083-1.507 1.063 1.063 0 0 1 1.503-.083l6.217 5.583a1.07 1.07 0 0 1 .002 1.587l-6.27 5.672c-.437.395-1.11.36-1.504-.078a1.07 1.07 0 0 1 .078-1.507Z'),
            'default': this.getIcon(16, 16, 'default', '#000','M 0 0 L 100 0 L 100 100 L 0 100 Z')
        };
    }

    getIcon(height, width, title, color, path) {
        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="${height}" width="${width}">
                    <title>${title}</title>
                    <path fill="none" stroke="${color}" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="${path}"></path>
                </svg>`;
    }

    updateIcon() {
        switch (getClassNameFromTag(this.link)) {
            case 'desc':
                this.link.innerHTML = this.icons['desc'];
                break;
            case 'asc':
                this.link.innerHTML = this.icons['asc'];
                break;
            case 'no-sort':
                this.link.innerHTML = this.icons['no-sort'];
                break;
            case 'back':
                this.link.innerHTML = this.icons['back'];
                break;
            case 'front':
                this.link.innerHTML = this.icons['front'];
                break;
            default:
                this.link.innerHTML = this.icons['default'];
        }
    }
}

/**
 * Для кастомизации заголовка таблицы переносим ссылку с заголовка на иконку
 */
document.addEventListener('DOMContentLoaded', function () {
    const thElements = document.querySelectorAll('.grid-view th');

    thElements.forEach(th => {
        const link = th.querySelector('a');
        if (link) {
            link.classList.add('no-sort');
            /*const textDiv = document.createElement('div');
            const iconDiv = document.createElement('div');
            textDiv.innerHTML = link.innerHTML;
            const iconToggle = new IconToggle(link);
            iconToggle.updateIcon();
            iconDiv.appendChild(link);
            //th.innerHTML = ''; // очищаем <th> перед добавлением новых блоков
            th.appendChild(textDiv);
            th.appendChild(iconDiv);
            textDiv.style.display = 'inline-block';
            iconDiv.style.display = 'inline-block';
            textDiv.style.verticalAlign = 'middle';*/
            th.innerHTML = link.innerHTML;
            const iconToggle = new IconToggle(link);
            iconToggle.updateIcon();
            th.appendChild(link);
        }
    });
});

/**
 * Добавление в breadcrumb ссылки вернуться назад
 */
document.addEventListener('DOMContentLoaded', function () {
    const previousPage = document.referrer;

    if (previousPage) {
        const newItem = document.createElement('li');
        newItem.classList.add('breadcrumb-item');

        const newLink = document.createElement('a');
        newLink.href = previousPage;

        newLink.classList.add('back');
        const iconToggle = new IconToggle(newLink);
        iconToggle.updateIcon();
        newItem.appendChild(newLink);

        const firstItem = document.querySelector('.breadcrumb-item');

        if (firstItem) {
            firstItem.parentNode.insertBefore(newItem, firstItem);
        }
    }
});

/**
 * Отображение панели фильтров
 */
document.addEventListener('DOMContentLoaded', function () {
    const filterToggle = document.getElementById('filterToggle');
    const filterPanel = document.getElementById('filterPanel');
    if (filterToggle && filterPanel)
    {
        const pathSVG = filterToggle.getElementsByTagName('path');
        let color = '#000';

        if (window.location.href.includes('Search'))
        {
            filterPanel.style.display = 'block';
        }

        filterToggle.addEventListener('click', function () {
            if (filterPanel.style.display === 'none' || filterPanel.style.display === '') {
                filterPanel.style.display = 'block';
                color = '#009580';
            } else {
                filterPanel.style.display = 'none';
                color = '#000';
            }
            for (let i = 0; i < pathSVG.length; i++) {
                pathSVG[i].setAttribute('stroke', color);
            }
        });
    }
});

/**
 * Открытие view при нажатии на строку в таблице
 */
$(document).ready(function() {
    $('.table').on('click', 'tr', function() {
        let url = $(this).data('href');
        if (url) {
            window.location.href = url;
        }
    });

    $('.table tr[data-href]').addClass('tr-link');
});

/**
 * Открытие модального окна для создания резервов исходящей документации
 */
$(window).on('load', function() {
    $('#open-modal').click(function(event) {
        event.preventDefault();
        $('#modal-reserve').modal('show');
    });
});

/**
 * Изменение стандартной пагинации
 */
document.addEventListener('DOMContentLoaded', function () {
    const paginationItems = document.querySelectorAll('.pagination li:not(.prev):not(.next)');
    const currentPageItem = Array.from(paginationItems).find(item => item.classList.contains('active'));

    if (currentPageItem) {
        const currentPage = currentPageItem.textContent.trim();
        const input = document.createElement('input');
        input.type = 'text';
        input.value = currentPage;
        const textNode = document.createTextNode(`/  ${totalPages} `);

        currentPageItem.innerHTML = '';
        currentPageItem.appendChild(input);
        currentPageItem.appendChild(textNode);

        input.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                const page = parseInt(input.value, 10);
                if (!isNaN(page) && page > 0) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', page);
                    window.location.href = url.toString();
                }
            }
        });

        const prevItem = document.querySelector('li.prev').firstElementChild;
        const nextItem = document.querySelector('li.next').firstElementChild;

        if (prevItem) {
            prevItem.classList.add('back');
            const backToggle = new IconToggle(prevItem);
            backToggle.updateIcon();
        }

        if (nextItem) {
            nextItem.classList.add('front');
            const frontToggle = new IconToggle(nextItem);
            frontToggle.updateIcon();
        }
    }
});

document.addEventListener("DOMContentLoaded", function() {
    let accordionButtons = document.querySelectorAll(".accordion-btn");

    if (accordionButtons) {
        accordionButtons.forEach(function(button) {
            let represent = button.parentNode;
            let date = represent.nextElementSibling;
            let btnClose = date.nextElementSibling;

            button.addEventListener("click", function() {
                represent.style.display = "none";
                date.style.display = "block";
                btnClose.style.display = "block";
            });

            btnClose.addEventListener("click", function() {
                represent.style.display = "flex";
                date.style.display = "none";
                btnClose.style.display = "none";
            });
        });
    }

});