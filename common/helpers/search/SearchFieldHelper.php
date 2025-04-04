<?php

namespace common\helpers\search;

use common\helpers\DateFormatter;

class SearchFieldHelper
{
    const EMPTY_FIELD = -1;     // необходимо для проверки полей в фильтрах и в классах search
    const SIMPLE_ANSWER_ARRAY = [0 => 'Нет', 1 => 'Да'];

    /**
     * Создает поле фильтра типа date
     *
     * @param string $fieldName
     * @param string $label
     * @param string $placeholder
     * @return array[]
     */
    public static function dateField(string $fieldName, string $label, string $placeholder)
    {
        return [
            $fieldName => [
                'type' => 'date',
                'label' => $label,
                'placeholder' => $placeholder,
                'dateFormat' => 'php:d.m.Y',
                'autocomplete'=>'off',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => DateFormatter::DEFAULT_STUDY_YEAR_RANGE,
                ],
            ],
        ];
    }

    /**
     * Создает поле фильтра типа text
     *
     * @param string $fieldName
     * @param string $label
     * @param string $placeholder
     * @return array[]
     */
    public static function textField(string $fieldName, string $label, string $placeholder) {
        return [
            $fieldName => [
                'type' => 'text',
                'label' => $label,
                'placeholder' => $placeholder,
                'autocomplete'=>'off',
            ],
        ];
    }

    /**
     * Создает поле фильтра типа dropdown
     *
     * @param string $fieldName
     * @param string $label
     * @param array $data
     * @param string|null $prompt
     * @param string|null $defaultValue
     * @return array|array[]
     */
    public static function dropdownField(string $fieldName, string $label, array $data, ?string $prompt = null, ?string $defaultValue = null) {
        $result = [
            $fieldName => [
                'type' => 'dropdown',
                'label' => $label,
                'data' => $data,
            ],
        ];

        if ($prompt !== null) {
            $result[$fieldName]['prompt'] = $prompt;
        }

        if ($defaultValue !== null) {
            $result[$fieldName]['options'] = [$defaultValue => ['Selected' => true]];
        }

        return $result;
    }

    /**
     * Позволяет разместить html разметку как специальное поле
     *
     * @param string $fieldName
     * @param string $data
     * @return array[]
     */
    public static function specialHtmlFiled(string $fieldName, string $data) {
        return [
            $fieldName => [
                'type' => 'html',
                'data' => $data,
            ],
        ];
    }
}