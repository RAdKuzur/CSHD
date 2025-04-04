<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/old-style.css',
        'css/customized.css',
        'css/viewCard.css',
        'css/login.css',
        'css/main-index.css',
        'css/field.css',
        'css/media.css',
    ];
    public $js = [
        'js/customized.js',
        'js/login.js',
        //'js/field-view.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
