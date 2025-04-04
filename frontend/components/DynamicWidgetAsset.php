<?php

namespace app\components;

class DynamicWidgetAsset extends \yii\web\AssetBundle
{
    public $css = [
        'css/dynamic-widget.css',
    ];

    public $js = [
        'js/dynamic-widget.js',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}