<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
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
        'js/site.js',
        'js/index.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
    ];
}
