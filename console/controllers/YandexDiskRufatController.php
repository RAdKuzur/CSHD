<?php

namespace console\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;

class YandexDiskRufatController extends Controller
{

    public function actionPublish() {
        $file = UploadedFile::getInstanceByName('file');


        $file->saveAs();



        $rabbit = new RabbitMQRufat();
        $rabbit->init('localhost', );






    }




}