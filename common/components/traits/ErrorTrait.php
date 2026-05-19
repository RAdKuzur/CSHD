<?php

namespace common\components\traits;

use common\helpers\ErrorAssociationHelper;
use common\models\Error;
use common\models\work\ErrorsWork;
use common\repositories\general\ErrorsRepository;
use Yii;

trait ErrorTrait
{
    private ErrorsRepository $errorsTraitRepository;

    public function init(
        ErrorsRepository $errorsTraitRepository = null
    )
    {
        if (!$errorsTraitRepository) {
            $errorsTraitRepository = Yii::createObject(ErrorsRepository::class);
        }

        /** @var ErrorsRepository $errorsTraitRepository */
        $this->errorsTraitRepository = $errorsTraitRepository;
    }

    /**
     * Основной метод проверки моделей на ошибки
     *
     * @param array $allErrors массив ID ошибок, на которые должна быть проверена модель {@see ErrorAssociationHelper}
     * @param string $tableName имя таблицы модели
     * @param int $rowId ID строки в таблице
     * @return void
     */
    public function checkModel(array $allErrors, string $tableName, int $rowId)
    {
        $currentErrors = $this->errorsTraitRepository->getErrorsByTableRow($tableName, $rowId);

        // Сначала проверяем существующие ошибки - были ли они исправлены в результате действий пользователя
        foreach ($currentErrors as $currentError) {
            /** @var ErrorsWork $currentError */
            /** @var Error $errorEntity */
            $errorEntity = Yii::$app->errors->get($currentError->error);
            $errorEntity->fixError($currentError->id);
        }

        // Получаем все прощёные ошибки
        $amnestyErrors = $this->errorsTraitRepository->getAmnestyErrorsByTableRow($tableName, $rowId);
        $numberAmnestyErrors = [];
        // Записываем их номера
        foreach ($amnestyErrors as $error) {
            $numberAmnestyErrors[] = $error->error;
        }
        // Исключаем прощёные ошибки из повторной проверки
        $checkErrors = array_diff($allErrors, $numberAmnestyErrors);

        // Затем проверяем список ошибок для модели - появились ли ошибки в результате действий пользователя
        foreach ($checkErrors as $error) {
            /** @var ErrorsWork $amnestyErrors */
            /** @var Error $errorEntity */
            $errorEntity = Yii::$app->errors->get($error);
            $errorEntity->makeError($rowId);
        }

        // В конце проверяем все ошибки на изменение их состояния (с обычного на критическое)
        $newErrors = $this->errorsTraitRepository->getErrorsByTableRow($tableName, $rowId);

        foreach ($newErrors as $error) {
            /** @var ErrorsWork $error */
            /** @var Error $errorEntity */
            $errorEntity = Yii::$app->errors->get($error->error);
            if ($errorEntity->isChangeable()) {
                $errorEntity->changeState($error->id);
            }
        }
    }
    public function amnestyErrors(string $tableName, int $rowId): void
    {
        $errors = $this->errorsTraitRepository->getErrorsByTableRow($tableName, $rowId);
        foreach ($errors as $error) {
            $error->setAmnesty();
            $this->errorsTraitRepository->save($error);
        }
    }
}