<?php

namespace common\widgets\select_search;

use common\helpers\StringFormatter;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

class SelectSearch extends Widget
{
    public $model;
    public $attribute;
    public $data = [];
    public $placeholder = 'Введите текст для поиска...';

    public function run()
    {
        $inputId = Html::getInputId($this->model, $this->attribute);
        $optionKeys = array_keys($this->data);
        $optionVals = array_values($this->data);

        $combinedArray = Json::htmlEncode(
            array_map(function($key, $value) {
                return [$key, $value];
            }, $optionKeys, $optionVals)
        );

        $currentValue = $this->model->{$this->attribute};
        $currentText = $this->data[$currentValue] ?? '';

        $this->registerAssets($inputId, $combinedArray);

        return Html::activeDropDownList($this->model, $this->attribute, [], [
                'id' => $inputId,
                'class' => 'form-control select-search',
                'prompt' => $this->placeholder,
                'style' => 'display:none;',
                'name' => StringFormatter::getLastSegmentByBackslash(get_class($this->model)) . '[' . $this->attribute . ']',
            ]) . Html::textInput(StringFormatter::getLastSegmentByBackslash(get_class($this->model)) . '[' . $this->attribute . ']', $currentText, [
                'placeholder' => $this->placeholder,
                'class' => 'form-control search-choice-result',
                'autocomplete' => 'off',
                'id' => $inputId . '_choice',
                'readonly' => true,
            ]) . Html::textInput(StringFormatter::getLastSegmentByBackslash(get_class($this->model)) . '[' . $this->attribute . ']', $currentValue, [
                'placeholder' => $this->placeholder,
                'class' => 'form-control search-input choice',
                'autocomplete' => 'off',
                'style' => 'display:none;',
                'id' => $inputId . '_input',
            ]) . Html::tag('div', '', ['class' => 'search-results']);
    }

    protected function registerAssets($inputId, $options)
    {
        $view = $this->getView();

        $js = <<<JS
        var data = $options;
        $(document).ready(function() {
            var modelValue = $('#{$inputId}').val();
            console.log(modelValue);
            $('#{$inputId}_input').val(modelValue);
            var selectedItem = data.find(item => item[0] === modelValue);
            if (selectedItem) {
                $('#{$inputId}_choice').val(selectedItem[1]);
            }
        });
        $('#{$inputId}_choice').on('keyup focus', function() {
            $('.choice').css('display', 'block').val('');
            var value = $(this).val().toLowerCase();
            var results = data.filter(item => item[1].toLowerCase().includes(value));
            var resultsDiv = $('.search-results');
            resultsDiv.empty();
            $.each(results, function(index, item) {
                if (item) {
                    resultsDiv.append('<div class="result-item" data-value="' + item[0] + '">' + item[1] + '</div>');
                }
            });
        });
        $('#{$inputId}_input').on('keyup focus', function() {
            $(this).next('.choice').css('display', 'block');
            var value = $(this).val().toLowerCase();
            var resultsDiv = $(this).next('.search-results');
            resultsDiv.empty();
            var results = data.filter(item => item[1].toLowerCase().includes(value));
            $.each(results, function(index, item) {
                if (item) {
                    resultsDiv.append('<div class="result-item" data-value="' + item[0] + '">' + item[1] + '</div>');
                }
            });
        });
        $(document).on('click', '.result-item', function() {    
            var selectedValue = $(this).data('value'); 
            var selectedText = $(this).text(); 
            $('#{$inputId}_input').val(selectedValue);    
            $('#{$inputId}_choice').val(selectedText);  
            $('#{$inputId}').val(selectedValue); 
            console.log(selectedValue);
            $('.search-results').empty();
            $('.choice').css('display', 'none');
        });
        $(document).on('focusin', function(event) {    
            if ($(event.target).closest('.search-input, .search-choice-result, .search-results').length === 0) { 
                $('.search-results').empty(); 
                $('.choice').css('display', 'none');
            }
        });
        // Новый обработчик для keyup, который будет скрывать результаты при нажатии на ESC
        $(document).on('keyup', function(event) {
            if (event.key === "Escape") {
                $('.search-results').empty();
                $('.choice').css('display', 'none');
            }
        });
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.search-input, .search-choice-result, .search-results').length) {
                $('.search-results').empty(); 
                $('.choice').css('display', 'none');
            }
        });
        JS;

        $css = <<<CSS
            .search-input {
                width: 100%; /* Делаем поле ввода на всю ширину */
                padding: 10px; /* Добавляем отступы */
                border: 1px solid #ccc; /* Граница */
                border-radius: 4px; /* Закругления углов */
                background-color: #fff; /* Белый фон */
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Нежная тень */
                transition: border-color 0.3s; /* Плавный переход для цвета границы */
            }
            
            .search-input:focus {
                border-color: #007bff; /* Цвет границы при фокусе */
                outline: none; /* Убираем стандартный контур */
            }
            
            /* Стили для выпадающих результатов */
            .search-results {
                border: 1px solid #ccc; /* Граница для списка результатов */
                border-radius: 0 0 4px 4px; /* Закругления углов снизу */
                display: block; /* Скрываем результаты по умолчанию */
                background-color: #fff; /* Белый фон */
                position: absolute; /* Позиционируем абсолютно */
                z-index: 1000; /* На переднем плане */
                width: calc(90% - 2px); /* Делаем ширину такой же, как у текстового поля */
                max-height: 400px;
                overflow-y: scroll;
            }
            
            .search-results div {
                padding: 10px; /* Отступы для каждого результата */
                cursor: pointer; /* Указатель при наведении */
            }
            
            .search-results div:hover {
                background-color: #f1f1f1; /* Подсветка результата при наведении */
            }
        CSS;


        $view->registerJs($js);
        $view->registerCss($css);
    }
}