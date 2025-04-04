<?php

namespace app\components;

use yii\grid\ActionColumn;
use yii\helpers\Html;

class VerticalActionColumn extends ActionColumn
{
    public $defaultButtons = ['view', 'update', 'delete'];
    public $customIcons = [
        'view' => '<span class="icon-view"></span>',
        'update' => '<span class="icon-update"></span>',
        'delete' => '<span class="icon-delete"></span>',
    ];
    protected function renderDataCellContent($model, $key, $index)
    {
        return preg_replace_callback('/\\{([\w\-\/]+)\\}/', function ($matches) use ($model, $key, $index) {
            $name = $matches[1];

            if (isset($this->buttons[$name])) {
                $url = $this->createUrl($name, $model, $key, $index);
                return '<div>' . call_user_func($this->buttons[$name], $url, $model, $key) . '</div>';
            }

            return '';
        }, $this->template);
    }
}