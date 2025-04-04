<?php

namespace common\helpers\html;

use common\helpers\common\BaseFunctions;
use common\helpers\DateFormatter;
use common\helpers\files\FilePaths;
use common\helpers\StringFormatter;
use common\models\Error;
use common\models\work\ErrorsWork;
use common\repositories\general\ErrorsRepository;
use frontend\models\work\dictionaries\ForeignEventParticipantsWork;
use frontend\models\work\dictionaries\PersonalDataParticipantWork;
use frontend\models\work\educational\training_group\TrainingGroupParticipantWork;
use frontend\models\work\event\ParticipantAchievementWork;
use frontend\models\work\team\SquadParticipantWork;
use InvalidArgumentException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

class HtmlBuilder
{
    const DATE_FIELD_TYPE = 'date';
    const TEXT_FIELD_TYPE = 'text';
    const DROPDOWN_FIELD_TYPE = 'dropdown';

    const SVG_PRIMARY_COLOR = 'svg-primary';
    const SVG_CRITICAL_COLOR = 'svg-critical';

    const TYPE_WARNING = 'warning';
    const TYPE_INFO = 'info';

    /**
     * Добваляет кнопку, которая следует за пользователем и при нажатии отправляет его наверх
     * @return string
     */
    public static function upButton()
    {
        return '<button class="btn-secondary" id="toTopButton">'.self::paintSVG(FilePaths::SVG_UP).'</button>
                <script type="text/javascript" src="/js/toUpButton.js"></script>';
    }

    /**
     * Окрашивает иконку в нужный цветовой стиль
     * @param string $svgLink
     * @param string $svgColorClass
     * @return string
     */
    public static function paintSVG(string $svgLink, string $svgColorClass = '')
    {
        return '<span class="'.$svgColorClass.'">'. file_get_contents($svgLink) . '</span>';
    }

    /**
     * Создает подсказчик с нужной иконкой и внутренним сообщением которое отображается при наведении
     *
     * @param string $content
     * @param string|null $svgLink
     * @param string|null $svgColorClass
     * @return string
     */
    public static function createTooltipIcon(string $content, string $svgLink = FilePaths::SVG_INFO, string $svgColorClass = '')
    {
        return '<span class="tooltip-span">
                    '.self::paintSVG($svgLink, $svgColorClass).'
                    <div class="ant-tooltip">
                        <div class="ant-tooltip-arrow"></div>
                        <div class="ant-tooltip-content">
                            '.$content.'
                        </div>
                    </div>
                </span>';
    }

    /**
     * Подсказчик текста над текстом
     * @param string $content
     * @param string $contentTooltip
     * @return string
     */
    public static function createTooltip(string $content, string $contentTooltip)
    {
        return '<span class="tooltip-span">
                    <div class="content">
                        '.$content.'
                    </div>
                    <div class="ant-tooltip">
                        <div class="ant-tooltip-arrow"></div>
                        <div class="ant-tooltip-content">
                            '.$contentTooltip.'
                        </div>
                    </div>
                </span>';
    }

    /**
     * Добавляет начертание шрифта для подзаголовка и уточнения
     *
     * @param string $subtitle
     * @param string $clarification
     * @param string $classSubtitle
     * @param string $classClarification
     * @return string
     */
    public static function createSubtitleAndClarification(string $subtitle, string $clarification, string $classSubtitle = 'fnt-wght-5', string $classClarification = 'fnt-wght-2')
    {
        return '<span class="' . $classSubtitle . '">' . $subtitle . '</span><span class="' . $classClarification . '">'. $clarification . '</span>';
    }

    /**
     * Создает красивый переключатель для чекбокса
     *
     * @param string $offSwitchText
     * @param string $onSwitchText
     * @param string $idElement
     * @param string|null $nameInput
     * @param bool $checked
     * @return string
     */
    public static function createToggle(string $offSwitchText, string $onSwitchText, string $idElement, string $nameInput = null, bool $checked = false)
    {
        return '<div class="toggle-wrapper form-group '.$nameInput.'">
                    <span class="toggle-icon off">'.$offSwitchText.'</span>
                    <div class="toggle-switcher">
                        <input type="hidden" value="0" name="'.$nameInput.'">
                        <input type="checkbox" value="1" id="'.$idElement.'" name="'.$nameInput.'" '.($checked ? 'checked' : '').'/>
                        <label for="'.$idElement.'"></label>
                    </div>
                    <span class="toggle-icon on">'.$onSwitchText.'</span>
                </div>';
    }

    /**
     * Создает красивое представление длинного контента
     * в виде сложенного набора заголовка и кнопки для полного отображения
     * @param string $content
     * @param int $lengthPrev
     * @param string $textBtnOpen
     * @param string $textBtnClose
     * @return string
     */
    public static function createAccordion(string $content, int $lengthPrev = 20, string $textBtnOpen = 'Развернуть', string $textBtnClose = 'Скрыть')
    {
        $contentString = strip_tags($content);
        $result = '<div class="accordion-block">
                        <div class="flexx space represent">
                            <div class="prev-accordion">' . mb_substr($contentString, 0, $lengthPrev) . '...</div>
                            <button class="accordion-btn btn-secondary">' . $textBtnOpen . '</button>
                        </div>
                        <div class="accordion-date">
                        '. $content .'
                        </div>
                        <button class="accordion-btn-close btn-secondary">'. $textBtnClose .'</button>
                   </div>';
        return $result;
    }

    /**
     * Превращает массив в разметку с разделителем <br>
     * и возвращает их в виде аккордиона
     * @param array $content
     * @param int $lengthPrev
     * @param string $textBtnOpen
     * @param string $textBtnClose
     * @return string
     */
    public static function arrayToAccordion(array $content, int $lengthPrev = 20, string $textBtnOpen = 'Развернуть', string $textBtnClose = 'Скрыть')
    {
        $content = implode('<br>', $content);
        return self::createAccordion($content, $lengthPrev, $textBtnOpen, $textBtnClose);
    }

    /**
     * Метод создания массива option-s для select
     * $items должен иметь поля $id и $name
     * @param $items
     * @return string
     */
    public static function buildOptionList($items)
    {
        $result = '';
        foreach ($items as $item) {
            $result .= "<option value='" . $item->id . "'>" . $item->name . "</option>";
        }
        return $result;
    }

    /**
     * Добавляет пустое значение в список выпадающего списка
     * @param string $text
     * @return string
     */
    public static function createEmptyOption(string $text = '---')
    {
        return "<option value>{$text}</option>";
    }

    /**
     * Создает таблицу разрешений на разглашение ПД
     * @param array $data
     * @return string
     */
    public static function createPersonalDataTable(array $data)
    {
        $result = "<table class='table table-bordered' style='width: 600px'>";
        foreach (Yii::$app->personalData->getList() as $key => $pd)
        {
            $result .= '<tr><td style="width: 350px">';
            if (!in_array($key, $data)) {
                $result .= $pd.'</td><td style="width: 250px"><span class="badge badge-success">Разрешено</span></td>';
            }
            else {
                $result .= $pd.'</td><td style="width: 250px"><span class="badge badge-error">Запрещено</span></td>';
            }
            $result .= '</td></tr>';
        }
        $result .= "</table>";

        return $result;
    }

    /**
     * Создает группу кнопок
     * $linksArray должен быть ассоциативным массивом ['Имя кнопки' => ['url' => ['ссылка'], 'class' => '...', 'data' => [...] ], ...]
     * параметры class и data являются не обязательными
     * @param array $linksArray
     * @return string
     */
    public static function createGroupButton(array $linksArray)
    {
        $result = '<div class="button-group">';

        foreach ($linksArray as $label => $linkOptions) {
            $url = $linkOptions['url'];
            $class = $linkOptions['class'] ?? 'btn-secondary'; // Класс по умолчанию
            $data = $linkOptions['data'] ?? [];
            $id = $linkOptions['id'] ?? '';

            $options = ['class' => [$class], 'data' => $data];
            if ($id !== '') {
                $options['id'] = $id;
            }

            $result .= Html::a($label, $url, $options);
        }

        $result .= '</div>';
        return $result;
    }

    /**
     * Создает кнопки для фильтрации(поиска) и очистки параметров(переход к чистому индексу)
     * @param string $resetUrl     // url куда возвращаться по кнопке очистки параметров
     * @return string
     */
    public static function filterButton(string $resetUrl) {
        return '<div class="form-group-button">
                    <button type="submit" class="btn btn-primary">Поиск</button>
                    <a href="'.Url::to([$resetUrl]).'" type="reset" class="btn btn-secondary" style="font-weight: 500;">Очистить</a>
                </div>';
    }

    /**
     * Создает панель фильтров на _search страницах. Обязательно наличие HtmlCreator::filterToggle() на странице отображения (index)
     * @param object $searchModel
     * @param array $searchFields
     * @param ActiveForm $form
     * @param int $valueInRow   // количество элементов поиска в строке
     * @param string $resetUrl // является кнопкой сброса фильтров
     * @return string
     * @throws \Exception
     */
    public static function createFilterPanel(object $searchModel, array $searchFields, ActiveForm $form, int $valueInRow, string $resetUrl)
    {
        $result = '<div class="filter-panel" id="filterPanel">
                        '.HtmlCreator::filterHeaderForm().'
                        <div class="filter-date">';

        $counter = 0;
        $count = count($searchFields);
        foreach ($searchFields as $attribute => $field) {
            if ($counter % $valueInRow == 0) {
                $result .= '<div class="flexx">';
            }
            $counter++;

            $result .= '<div class="filter-input">';
            $options = [
                'placeholder' => $field['placeholder'] ?? '',
                'class' => 'form-control',
                'autocomplete' => 'off',
            ];

            /** @var  \yii\base\Model $searshModel */
            switch ($field['type']) {
                case self::DATE_FIELD_TYPE:
                    $widgetOptions = [
                        'dateFormat' => $field['dateFormat'],
                        'language' => 'ru',
                        'options' => $options,
                        'clientOptions' => $field['clientOptions'],
                    ];
                    $result .= $form->field($searchModel, $attribute)->widget(DatePicker::class, $widgetOptions)->label(false);
                    break;
                case self::TEXT_FIELD_TYPE:
                    $result .= $form->field($searchModel, $attribute)->textInput($options)->label(false);
                    break;
                case self::DROPDOWN_FIELD_TYPE:
                    $options['prompt'] = $field['prompt'];
                    $options['options'] = $field['options'];
                    $result .= $form->field($searchModel, $attribute)->dropDownList($field['data'], $options)->label(false);
                    break;
                default:
                    $result .= '<div class="special-field">' . $field['data'] . '</div>';
            }

            $result .= '</div>';

            if ($counter % $valueInRow == 0 || $counter === $count) {
                $result .= '</div>';
            }
        }
        $result .= self::filterButton($resetUrl) . '</div>
            </div>';
        return $result;
    }

    /**
     * Создает таблицу с данными из $dataMatrix и экшн-кнопками из $buttonMatrix
     * Первые элементы массивов $dataMatrix - названия столбцов
     * @param array $dataMatrix данные для таблицы в виде матрицы
     * @param array $buttonMatrix матрица кнопок взаимодействия класса HtmlHelper::a()
     * @param array $classes css-классы для стилизации таблицы
     * @return string
     */
    public static function createTableWithActionButtons(
        array $dataMatrix,
        array $buttonMatrix,
        array $classes = ['table' => 'table table-bordered', 'tr' => '', 'th' => '', 'td' => ''])
    {
        if (count($buttonMatrix) == 0 || count($buttonMatrix[0]) == 0) {
            return '';
        }

        $result = '<table class="' . $classes['table'] . '"><thead>';
        foreach ($dataMatrix as $row) {
            $result .= "<th class='" . $classes['th'] . "'>$row[0]</th>";
        }
        $result .= '</thead>';

        $dataMatrix = BaseFunctions::transposeMatrix($dataMatrix);
        $buttonMatrix = BaseFunctions::transposeMatrix($buttonMatrix);

        foreach ($dataMatrix as $i => $row) {
            if ($i > 0) {
                $result .= '<tr class="' . $classes['tr'] . '">';
                foreach ($row as $cell) {
                    $result .= "<td class='" . $classes['td'] . "'>$cell</td>";
                }
                foreach ($buttonMatrix[$i - 1] as $button) {
                    $result .= "<td class='" . $classes['td'] . "'>$button</td>";
                }
                $result .= '</tr>';
            }
        }

        $result .= '</table>';

        return $result;
    }

    /**
     * Создает массив кнопок с указанными в $queryParams параметрами
     * @param string $text имя кнопок
     * @param string $url url кнопок
     * @param array $queryParams массив параметров вида ['param_name' => [1, 2, 3], 'param_name' => ['some', 'data'], ...]
     * @return array
     */
    public static function createButtonsArray(string $text, string $url, array $queryParams)
    {
        $result = [];

        $keys = array_keys($queryParams);
        $maxLength = max(array_map('count', $queryParams));

        // Формируем результирующий массив
        for ($i = 0; $i < $maxLength; $i++) {
            $combined = [];
            foreach ($keys as $key) {
                if (isset($queryParams[$key][$i])) {
                    $combined[$key] = $queryParams[$key][$i];
                }
            }
            if (!empty($combined)) {
                $result[] = Html::a($text, array_merge([$url], $combined));
            }
        }

        return $result;
    }

    /**
     * Добавляет столбец чекбоксов к таблице
     * @param string $formAction экшн для формы
     * @param string $submitContent текст кнопки сабмита
     * @param string $checkName имя для полей формы (чекбоксов)
     * @param array $checkValues массив значений для чекбоксов
     * @param string $table исходная таблица
     * @param array $classes массив классов для стилизации формата ['submit' => 'classname']
     * @return string
     */
    public static function wrapTableInCheckboxesColumn(
        string $formAction,
        string $submitContent,
        string $checkName,
        array $checkValues,
        string $table,
        array $classes = ['submit' => 'btn btn-success']
    ) {
        // Находим все строки таблицы
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/s', $table, $matches);
        $rows = $matches[0];

        // Создаем массив чекбоксов
        $checkboxes = [];
        foreach ($checkValues as $key => $value) {
            $checkboxes[$key] = "<input type='hidden' name='$checkName' value='0'>".
                "<input type='checkbox' id='traininggroupwork-delarr$key' class='check' name='$checkName' value='$value'>";

            // Добавляем чекбокс в начало каждой строки
            $rows[$key] = preg_replace('/<tr[^>]*>/', "<tr><td>$checkboxes[$key]</td>", $rows[$key]);
        }

        $newHtmlTable = str_replace($matches[0], $rows, $table);

        preg_match_all('/<thead[^>]*>(.*?)<\/thead>/s', $newHtmlTable, $matches);
        $thead = $matches[0][0];
        $newTh = '<th class=""><input type="checkbox" class="checkbox-group"></th>';
        $newHtmlString = str_replace('<thead>', '<thead>' . $newTh, $thead);
        $newHtmlTable = preg_replace('/(<thead>.*?<\/thead>)/s', $newHtmlString, $newHtmlTable);

        $newClass = 'table-checkbox';
        $newHtmlString = preg_replace_callback(
            '/<table([^>]*)>/i',
            function ($matches) use ($newClass) {
                $attributes = $matches[1]; // Содержимое между <table и >

                // Если атрибут class уже существует
                if (preg_match('/class\s*=\s*"([^"]*)"/i', $attributes, $classMatch)) {
                    $classes = explode(' ', trim($classMatch[1]));

                    // Добавляем новый класс, если его еще нет
                    if (!in_array($newClass, $classes)) {
                        $classes[] = $newClass;
                    }

                    // Обновляем атрибут class
                    $updatedAttributes = preg_replace('/class\s*=\s*"[^"]*"/i', 'class="' . implode(' ', $classes) . '"', $attributes);

                    return '<table' . $updatedAttributes . '>';
                } else {
                    // Если атрибута class нет, добавляем его
                    return '<table' . $attributes . ' class="' . $newClass . '">';
                }
            },
            $newHtmlTable
        );

        $newHtmlTable = $newHtmlString;

        return self::wrapInForm($formAction, $submitContent, $newHtmlTable, $classes);
    }

    /**
     * Оборачивает в форму какой-либо контент
     * @param string $formAction экшн формы
     * @param string $submitContent текст кнопки сабмита
     * @param string $content контент, который необходимо обернуть в форму
     * @param array $classes массив классов для стилизации формата ['submit' => 'classname']
     * @return string
     */
    public static function wrapInForm(
        string $formAction,
        string $submitContent,
        string $content,
        array $classes = ['submit' => 'btn btn-success']
    )
    {
        $csrfToken = Yii::$app->request->getCsrfToken();
        $result = "<form action='$formAction' method='post'>";
        $result .=  Html::hiddenInput('_csrf-frontend', $csrfToken);
        $result .= $content;
        $result .= Html::submitButton($submitContent, ['class' => $classes['submit']]);
        $result .= "</form>";

        return $result;
    }

    /**
     * Создает информационное сообщение
     *
     * @param string $typeMessage
     * @param string $regularMessage
     * @param string $boldMessage
     * @return string
     */
    public static function createMessage(string $typeMessage, string $regularMessage, string $boldMessage = '')
    {
        switch ($typeMessage) {
            case self::TYPE_WARNING:
                $htmlClass = 'alert-warning';
                $content = '<strong>'. $boldMessage .'</strong> ' . $regularMessage;
                $svgContent = file_get_contents(FilePaths::SVG_ALERT_WARNING);
                $svgColorClass = '';
                break;
            case self::TYPE_INFO:
                $htmlClass = 'alert-info';
                $content = $regularMessage . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                $svgContent = file_get_contents(FilePaths::SVG_ALERT_INFO);
                $svgColorClass = '';
                break;
            default:
                throw new InvalidArgumentException('Не распознан тип сообщения');
        }

        return '<div class="alert alert-dismissible fade show ' . $htmlClass . '"  role="alert">
                    <span class="'.$svgColorClass.'">'. $svgContent . '</span>
                    '.$content.'
                </div>';
    }

    /**
     * Создаем изображение файла со ссылкой для view представлений
     * @param $url
     * @return string
     */
    public static function createSVGLink($url)
    {
        $title = 'скачать файл';
        $svgFile = FilePaths::SVG_FILE_DOWNLOAD;
        if ($url === '#') {
            $svgFile = FilePaths::SVG_FILE_NO_DOWNLOAD;
            $title = 'файл отсутствует';
        }
        $svgContent = file_get_contents($svgFile);

        $result = '<div class="fileIcon">';
        $result .= '<a href="' . $url .'" class="download" title="'. $title.'">' . $svgContent . '</a>';
        $result .= '</div>';
        return $result;
    }

    /**
     * @param ForeignEventParticipantsWork $participant1
     * @param ForeignEventParticipantsWork $participant2
     * @param TrainingGroupParticipantWork[] $groups1
     * @param TrainingGroupParticipantWork[] $groups2
     * @param SquadParticipantWork[] $events1
     * @param SquadParticipantWork[] $events2
     * @param ParticipantAchievementWork[] $achieves1
     * @param ParticipantAchievementWork[] $achieves2
     * @param PersonalDataParticipantWork[] $personalData1
     * @param PersonalDataParticipantWork[] $personalData2
     * @return string
     */
    public static function createMergeParticipantsTable(
        ForeignEventParticipantsWork $participant1,
        ForeignEventParticipantsWork $participant2,
        array $groups1,
        array $groups2,
        array $events1,
        array $events2,
        array $achieves1,
        array $achieves2,
        array $personalData1,
        array $personalData2
    )
    {
        $result = '<table class="table table-striped table-bordered detail-view" style="width: 91%">
            <tr><td><b>Фамилия</b></td><td id="td-secondname-1" style="width: 45%">'.$participant1->surname.'</td><td><b>Фамилия</b></td><td style="width: 45%">'.$participant2->surname.'</td></tr>
            <tr><td><b>Имя</b></td><td id="td-firstname-1" style="width: 45%">'.$participant1->firstname.'</td><td><b>Имя</b></td><td style="width: 45%">'.$participant2->firstname.'</td></tr>
            <tr><td><b>Отчество</b></td><td id="td-patronymic-1" style="width: 45%">'.$participant1->patronymic.'</td><td><b>Отчество</b></td><td style="width: 45%">'.$participant2->patronymic.'</td></tr>
            <tr><td><b>Пол</b></td><td id="td-sex-1" style="width: 45%">'.$participant1->getSexString().'</td><td><b>Пол</b></td><td style="width: 45%">'.$participant2->getSexString().'</td></tr>
            <tr><td><b>Дата рождения</b></td><td id="td-birthdate-1" style="width: 45%">'.$participant1->birthdate.'</td><td><b>Дата рождения</b></td><td style="width: 45%">'.$participant2->birthdate.'</td></tr>';

        $links1 = '';
        foreach ($groups1 as $group) {
            $links1 .= self::createGroupParticipantBlock($group);
        }

        $links2 = '';
        foreach ($groups2 as $group) {
            $links2 .= self::createGroupParticipantBlock($group);
        }

        $result .= '<tr><td><b>Группы</b></td><td style="width: 45%">'.$links1.'</td><td><b>Группы</b></td><td style="width: 45%">'.$links2.'</td></tr>';

        $eventsLink1 = '';
        foreach ($events1 as $event) {
            $eventsLink1 .= self::createActParticipantBlock($event);
        }

        $eventsLink2 = '';
        foreach ($events2 as $event) {
            $eventsLink2 .= self::createActParticipantBlock($event);
        }

        $result .= '<tr><td><b>Мепроприятия</b></td><td style="width: 45%">'.$eventsLink1.'</td><td><b>Мепроприятия</b></td><td style="width: 45%">'.$eventsLink2.'</td></tr>';

        $achievesLink1 = '';
        foreach ($achieves1 as $achievement) {
            $achievesLink1 .= self::createAchievementBlock($achievement);
        }

        $achievesLink2 = '';
        foreach ($achieves2 as $achievement) {
            $achievesLink2 .= self::createAchievementBlock($achievement);
        }

        $result .= '<tr><td><b>Достижения</b></td><td style="width: 45%">'.$achievesLink1.'</td><td><b>Достижения</b></td><td style="width: 45%">'.$achievesLink2.'</td></tr>';

        $resultN = "<table class='table table-bordered'>";
        foreach ($personalData1 as $pd) {
            $resultN .= self::createPersonalDataBlock($pd);
        }
        $resultN .= "</table>";

        $resultN1 = "<table class='table table-bordered'>";
        foreach ($personalData2 as $pd) {
            $resultN1 .= self::createPersonalDataBlock($pd);
        }
        $resultN1 .= "</table>";

        $result .= '<tr><td><b>Разглашение ПД</b></td><td style="width: 45%">'.$resultN.'</td><td><b>Разглашение ПД</b></td><td style="width: 45%">'.$resultN1.'</td></tr>';
        $result .= '</table><br>';
        $result .= '<a id="fill1" style="display: block; width: 91%" onclick="FillEditForm()" class="btn btn-primary">Открыть форму редактирования</a>';

        return $result;
    }

    public static function createGroupParticipantBlock(TrainingGroupParticipantWork $groupParticipant)
    {
        return DateFormatter::format($groupParticipant->trainingGroupWork->start_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot) . ' - ' .
            DateFormatter::format($groupParticipant->trainingGroupWork->finish_date, DateFormatter::Ymd_dash, DateFormatter::dmY_dot) . ' | ' .
            StringFormatter::stringAsLink(
                "Группа {$groupParticipant->trainingGroupWork->number}",
                Url::to(['training-group/view', 'id' => $groupParticipant->training_group_id])
            ) .
            ($groupParticipant->trainingGroupWork->finish_date < date("Y-m-d") ?
                ' (группа завершила обучение)' :
                ' <div style="background-color: green; display: inline"><font color="white"> (проходит обучение)</font></div>') .
            ($groupParticipant->status == 'stub' ?
                ' | Переведен' :
                ' | Отчислен') . '<br>';
    }

    public static function createActParticipantBlock(SquadParticipantWork $squad)
    {
        return StringFormatter::stringAsLink(
            $squad->actParticipantWork->foreignEventWork->name,
            Url::to(['event/foreign-event/view', 'id' => $squad->actParticipantWork->foreign_event_id])
        ).'<br>';
    }

    public static function createAchievementBlock(ParticipantAchievementWork $achievement)
    {
        return $achievement->achievement.' &mdash; '.
            StringFormatter::stringAsLink(
                $achievement->actParticipantWork->foreignEventWork->name,
                Url::to(['foreign-event/view', 'id' => $achievement->actParticipantWork->foreign_event_id])
            ).
            ' ('.$achievement->actParticipantWork->foreignEventWork->begin_date.')'.'<br>';
    }

    public static function createPersonalDataBlock(PersonalDataParticipantWork $pd)
    {
        $result = '<tr><td style="width: 350px">'.Yii::$app->personalData->get($pd->personal_data);
        if ($pd->status == PersonalDataParticipantWork::STATUS_FREE) {
            $result .= '</td><td style="width: 250px"><span class="badge badge-success b1">Разрешено</span></td>';
        }
        else {
            $result .= '</td><td style="width: 250px"><span class="badge badge-error b1">Запрещено</span></td>';
        }
        $result .= '</td></tr>';

        return $result;
    }

    /**
     * Функция для создания "двойных кнопок".
     * Это кнопка, которая может быть в одном из двух состояний, в зависимости от условия.
     * Массивы должны иметь только 2 элемента, каждый из которых описывает свойства соответствующей кнопки
     * Состояние 1 - индекс 0 массивов
     * Состояние 2 - индекс 1 массивов
     *
     * @param string[] $buttonNames имена кнопок
     * @param string[] $urls эндпоинты кнопок
     * @param array[] $classes списки классов для кнопок
     * @param bool $condition условие выбора состояния (состояние 1 - если $condition = true)
     * @return string
     */
    public static function createDualityButton(array $buttonNames, array $urls, array $classes, bool $condition) : string
    {
        if ($condition) {
            return Html::a($buttonNames[0], $urls[0], ['class' => implode(' ', $classes[0])]);
        }
        else {
            return Html::a($buttonNames[1], $urls[1], ['class' => implode(' ', $classes[1])]);
        }
    }

    public static function createErrorsBlock(string $tableName, int $rowId)
    {
        $errors = (Yii::createObject(ErrorsRepository::class))->getErrorsByTableRow($tableName, $rowId);
        $errorsString = implode('<br>', array_map(function(ErrorsWork $error) {
            /** @var Error $errorTemplate */
            $errorTemplate = Yii::$app->errors->get($error->error);
            $critImg = '';
            if ($error->isCrititcal()) {
                $critImg = self::paintSVG(FilePaths::SVG_ALERT_WARNING, self::SVG_CRITICAL_COLOR);
                //$critImg = '<img src="' . FilePaths::ALERT_TRIANGLE . '" style="width: 25px">';
            }
            return "$critImg<b>Ошибка $errorTemplate->code</b>: $errorTemplate->description$critImg";
        }, $errors));

        return strlen($errorsString) > 0 ?
            '<div class="alert alert-dismissible fade show alert-danger" role="alert">
                '.$errorsString.'
            </div>' : '';
    }

    /**
     *
     *
     * @param ErrorsWork[] $errors
     * @return string
     */
    public static function createErrorsTable(array $errors) : string
    {
        $table = '<table class="table table-bordered"><tr><th>Код проблемы</th><th>Описание проблемы</th><th>Место возникновения</th><th>Отдел</th></tr>';
        foreach ($errors as $error) {
            /** @var Error $errorEntity */
            $errorEntity = Yii::$app->errors->get($error->error);
            $table .= '<tr>';
            $table .= '<td>' . $errorEntity->code . '</td>';
            $table .= '<td>' . $errorEntity->description . '</td>';
            $table .= '<td>' . $error->getEntityName() . '</td>';
            $table .= '<td>' . Yii::$app->branches->get($error->branch) . '</td>';
            $table .= '</tr>';
        }

        return $table;
    }
}