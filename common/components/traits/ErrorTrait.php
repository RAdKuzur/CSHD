<?php

namespace common\components\traits;

use common\helpers\ErrorAssociationHelper;
use common\models\Error;
use common\models\work\ErrorsWork;
use common\repositories\general\BatchErrorsRepository;
use common\repositories\general\ErrorsRepository;
use common\repositories\general\ErrorsRepositoryInterface;
use Yii;

trait ErrorTrait
{
    private ErrorsRepositoryInterface $errorsTraitRepository;

    public function init(ErrorsRepositoryInterface $errorsTraitRepository = null)
    {
        if (!$errorsTraitRepository) {
            $errorsTraitRepository = Yii::createObject(ErrorsRepository::class);
        }
        $this->errorsTraitRepository = $errorsTraitRepository;
    }

    /**
     * Установка кастомного репозитория (для batch-режима)
     */
    public function setErrorsRepository(ErrorsRepositoryInterface $repository): void
    {
        $this->errorsTraitRepository = $repository;
    }

    /**
     * Получение текущего репозитория
     */
    public function getErrorsRepository(): ErrorsRepositoryInterface
    {
        return $this->errorsTraitRepository;
    }

    public function checkModel(array $allErrors, string $tableName, int $rowId)
    {
        $currentErrors = $this->errorsTraitRepository->getErrorsByTableRow($tableName, $rowId);

        foreach ($currentErrors as $currentError) {
            $errorEntity = Yii::$app->errors->get($currentError->error);
            $errorEntity->fixError($currentError->id);
        }

        $amnestyErrors = $this->errorsTraitRepository->getAmnestyErrorsByTableRow($tableName, $rowId);
        $numberAmnestyErrors = [];
        foreach ($amnestyErrors as $error) {
            $numberAmnestyErrors[] = $error->error;
        }
        $checkErrors = array_diff($allErrors, $numberAmnestyErrors);

        foreach ($checkErrors as $error) {
            $errorEntity = Yii::$app->errors->get($error);
            $errorEntity->makeError($rowId);
        }

        $newErrors = $this->errorsTraitRepository->getErrorsByTableRow($tableName, $rowId);

        foreach ($newErrors as $error) {
            $errorEntity = Yii::$app->errors->get($error->error);
            if ($errorEntity->isChangeable()) {
                $errorEntity->changeState($error->id);
            }
        }
    }

    /**
     * Batch-метод проверки с предзагруженными данными
     *
     * @param array $allErrors - список ошибок для проверки
     * @param string $tableName - имя таблицы
     * @param int $rowId - ID записи
     * @param array $preloadedData - предзагруженные данные для всех ошибок
     */
    /**
     * Метод с предзагруженными данными и ошибками
     *
     * @param array $allErrors - список кодов ошибок для проверки
     * @param string $tableName - имя таблицы
     * @param int $rowId - ID записи
     * @param array $preloadedData - предзагруженные данные для make/fix [errorCode => data]
     * @param array|null $allCurrentErrors - ВСЕ текущие ошибки таблицы [rowId => [errorCode => ErrorsWork]]
     * @param array|null $allAmnestyErrors - ВСЕ амнистированные ошибки [rowId => [errorCode]]
     */
    public function checkModelWithData(
        array $allErrors,
        string $tableName,
        int $rowId,
        array $preloadedData = [],
        ?array $allCurrentErrors = null,
        ?array $allAmnestyErrors = null
    ) {
        // 1. Получаем текущие ошибки: из предзагруженных или из БД
        if ($allCurrentErrors !== null) {
            $currentErrors = $allCurrentErrors[$rowId] ?? [];
        } else {
            $currentErrors = $this->errorsTraitRepository->getErrorsByTableRow($tableName, $rowId);
        }

        // 2. Обрабатываем fix для существующих ошибок
        foreach ($currentErrors as $currentError) {
            $errorEntity = Yii::$app->errors->get($currentError->error);

            // Передаем предзагруженные данные если есть
            if (isset($preloadedData[$currentError->error])) {
                $errorEntity->fixError($currentError->id, $preloadedData[$currentError->error]);
            } else {
                $errorEntity->fixError($currentError->id);
            }
        }

        // 3. Получаем амнистированные ошибки: из предзагруженных или из БД
        if ($allAmnestyErrors !== null) {
            $numberAmnestyErrors = $allAmnestyErrors[$rowId] ?? [];
        } else {
            $amnestyErrors = $this->errorsTraitRepository->getAmnestyErrorsByTableRow($tableName, $rowId);
            $numberAmnestyErrors = [];
            foreach ($amnestyErrors as $error) {
                $numberAmnestyErrors[] = $error->error;
            }
        }

        $checkErrors = array_diff($allErrors, $numberAmnestyErrors);

        // 4. Обрабатываем make для ошибок, которых еще нет
        foreach ($checkErrors as $error) {
            // Пропускаем если ошибка уже существует
            if (isset($currentErrors[$error])) {
                continue;
            }

            $errorEntity = Yii::$app->errors->get($error);

            // Передаем предзагруженные данные если есть
            if (isset($preloadedData[$error])) {
                $errorEntity->makeError($rowId, $preloadedData[$error]);
            } else {
                $errorEntity->makeError($rowId);
            }
        }

        // 5. Проверяем changeable: используем тот же массив currentErrors
        foreach ($currentErrors as $error) {
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