<?php

namespace common\helpers;

use Exception;
use yii\helpers\Url;

class ButtonsFormatter
{
    const BTN_PRIMARY = 'btn-primary';      // Насыщенно-зеленая
    const BTN_SUCCESS = 'btn-success';      // Бледно-зеленая
    const BTN_DANGER = 'btn-danger';        // Насященно-красная
    const BTN_WARNING = 'btn-warning';      // Бледно-красная
    const BTN_SECONDARY = 'btn-secondary';  // Серая
    const BTN_DEFAULT = 'btn-default';      // Прозрачная (ссылка без фона)


    /**
     * Возвращает массив для кнопок редактировать и удалить в карточке
     *
     * @param int $id
     * @return array[]
     */
    public static function updateDeleteLinks (int $id, string $customUpdateUrl = 'update') {
        return [
            'Редактировать' => [
                'url' => [$customUpdateUrl, 'id' => $id],
                'class' => self::BTN_PRIMARY,
            ],
            'Удалить' => [
                'url' => ['delete', 'id' => $id],
                'class' => self::BTN_DANGER,
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                    'method' => 'post',
                ],
            ],
        ];
    }
    public static function generateOrderLinks(int $id, string $customUpdateUrl = 'update') {
        return [
            'Редактировать' => [
                'url' => [$customUpdateUrl, 'id' => $id],
                'class' => self::BTN_PRIMARY,
            ],
            'Удалить' => [
                'url' => ['delete', 'id' => $id],
                'class' => self::BTN_DANGER,
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этот элемент?',
                    'method' => 'post',
                ],
            ],
            'Сгенерировать документ' => [
                'url' => ['generate-order', 'id' => $id],
                'class' => self::BTN_PRIMARY,
            ],
        ];
    }
    /**
     * Две кнопки с классом primary
     *
     * @param string $linkFirst
     * @param string $linkSecond
     * @return array[]
     */
    public static function twoPrimaryLinks (string $linkFirst, string $linkSecond) {
        return [
            'Добавить документ' => [
                'url' => Url::to([$linkFirst]),
                'class' => self::BTN_PRIMARY
            ],
            'Добавить резерв' => [
                'url' => Url::to([$linkSecond]),
                'class' => self::BTN_PRIMARY
            ],
        ];
    }

    /**
     * Кнопка для контроллера и открытия модального окна
     *
     * @param string $link
     * @param string $targetNameModal
     * @return array[]
     */
    public static function primaryLinkAndModal (string $link, string $targetNameModal) {
        return [
            'Добавить документ' => [
                'url' => Url::to([$link]),
                'class' => self::BTN_PRIMARY
            ],
            'Добавить резерв' => [
                'id' => 'open-modal-reserve',
                'url' => '#',
                'class' => self::BTN_PRIMARY,
                'data' => [
                    'toggle' => 'modal',
                    'target' => $targetNameModal,
                ],
            ],
        ];
    }

    /**
     * Кнопка СОЗДАНИЯ с классом primary
     *
     * @param string $nameObjectOnButton
     * @return array[]
     */
    public static function primaryCreateLink (string $nameObjectOnButton)
    {
        return [
            'Добавить ' . $nameObjectOnButton => [
                'url' => ['create'],
                'class' => self::BTN_PRIMARY,
            ]
        ];
    }

    /**
     * Возвращает массив для создания одной кнопки
     *
     * @param string $nameButton
     * @param string $nameClasses
     * @param string $link
     * @return array[]
     */
    public static function anyOneLink(string $nameButton, string $link, string $nameClasses, string $id = '', $paramsLink = '')
    {
        $url = $paramsLink ? Url::to([$link] + $paramsLink) : Url::to([$link]);
        return [
            $nameButton => [
                'url' => StringFormatter::isEmpty($link) ? '#' : $url,
                'class' => $nameClasses,
                'id' => $id
            ]
        ];
    }

    /**
     * Создает форматированные параметры для генерации ссылки через Url::to()
     *
     * @param int $valueParameter
     * @param string $nameParameter
     * @return int[]
     */
    public static function createParameterLink(int $valueParameter, string $nameParameter = 'id')
    {
        return [$nameParameter => $valueParameter];
    }
}