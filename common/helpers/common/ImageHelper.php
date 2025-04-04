<?php

namespace common\helpers\common;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\web\UploadedFile;

class ImageHelper
{
    /**
     * Проверка загруженного изображения на соответствующий размер
     *
     * @param UploadedFile $file
     * @param int $standardWidth
     * @param int $standardHeight
     * @return bool
     */
    public static function checkImgSize(UploadedFile $file, int $standardWidth, int $standardHeight) : bool
    {
        $filename = $file->tempName;
        $imagine = new Imagine();
        $size = $imagine->open($filename)->getSize();

        return $standardHeight == $size->getHeight() && $standardWidth == $size->getWidth();
    }

    /**
     * Изменение размера изображения в соответствии с указанными значениями
     *
     * @param UploadedFile $file
     * @param int $standardWidth
     * @param int $standardHeight
     * @return ManipulatorInterface
     */
    public static function resizeImg(UploadedFile $file, int $standardWidth, int $standardHeight) : ManipulatorInterface
    {
        $sizeBox = new Box($standardWidth, $standardHeight);
        $fileTempName = $file->tempName;
        $imagine = new Imagine();
        $object = $imagine->open($fileTempName);
        return $object->resize($sizeBox);
    }
}