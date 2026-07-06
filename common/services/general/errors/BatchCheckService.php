<?php

namespace common\services\general\errors;

use common\repositories\general\BatchErrorsRepository;
use common\repositories\general\ErrorsRepository;
use ReflectionProperty;
use Yii;

class BatchCheckService
{
    private BatchErrorsRepository $batchRepository;
    private array $originalRepositories = [];
    private array $affectedTraitModels = [];
    private ?object $errorDictionary = null;
    private bool $isActive = false;

    /** @var array - предзагруженные ошибки [rowId => [errorCode => ErrorsWork]] */
    private array $preloadedErrors = [];

    /** @var array - предзагруженные амнистированные ошибки [rowId => [errorCode]] */
    private array $preloadedAmnestyErrors = [];

    public function __construct()
    {
        $this->batchRepository = new BatchErrorsRepository(new ErrorsRepository());
    }

    /**
     * Предзагрузка ВСЕХ ошибок таблицы
     */
    public function preloadTableErrors(string $tableName, array $rowIds = []): void
    {
        // Загружаем все активные ошибки
        $allErrors = $this->batchRepository->getErrorByTableName($tableName);

        $this->preloadedErrors = [];
        foreach ($allErrors as $error) {
            $this->preloadedErrors[$error->table_row_id][$error->error] = $error;
        }

        // Загружаем все амнистированные ошибки
        $allAmnesty = $this->batchRepository->getAllAmnestyErrorsByTableName($tableName);

        $this->preloadedAmnestyErrors = [];
        foreach ($allAmnesty as $amnesty) {
            $this->preloadedAmnestyErrors[$amnesty->table_row_id][] = $amnesty->error;
        }
    }

    /**
     * Получить предзагруженные ошибки
     */
    public function getPreloadedErrors(): array
    {
        return $this->preloadedErrors;
    }

    /**
     * Получить предзагруженные амнистированные ошибки
     */
    public function getPreloadedAmnestyErrors(): array
    {
        return $this->preloadedAmnestyErrors;
    }

    public function registerModels(array $models): void
    {
        foreach ($models as $model) {
            $this->affectedTraitModels[] = $model;
        }
    }

    public function enableBatchMode(): void
    {
        if ($this->isActive) return;

        $this->batchRepository->enableBatchMode();
        $this->errorDictionary = Yii::$app->get('errors');

        $this->replaceRepositoryInService('materialService');
        $this->replaceRepositoryInService('achieveService');
        $this->replaceRepositoryInService('documentService');
        $this->replaceRepositoryInService('journalService');
        $this->replaceRepositoryInService('changeableService');
        $this->replaceRepositoryInService('foreignEventParticipantService');

        foreach ($this->affectedTraitModels as $model) {
            $reflection = new ReflectionProperty($model, 'errorsTraitRepository');
            $reflection->setAccessible(true);
            $originalRepo = $reflection->getValue($model);
            $this->originalRepositories['model_' . spl_object_id($model)] = $originalRepo;
            $reflection->setValue($model, $this->batchRepository);
        }

        $this->isActive = true;
    }

    private function replaceRepositoryInService(string $servicePropertyName): void
    {
        try {
            $reflection = new ReflectionProperty($this->errorDictionary, $servicePropertyName);
            $reflection->setAccessible(true);
            $service = $reflection->getValue($this->errorDictionary);

            if ($service && property_exists($service, 'errorsRepository')) {
                $repoReflection = new ReflectionProperty($service, 'errorsRepository');
                $repoReflection->setAccessible(true);
                $this->originalRepositories[$servicePropertyName] = $repoReflection->getValue($service);
                $repoReflection->setValue($service, $this->batchRepository);
            }
        } catch (\Exception $e) {
            Yii::error("Failed to replace repository in {$servicePropertyName}: " . $e->getMessage());
        }
    }

    public function disableBatchMode(): void
    {
        if (!$this->isActive || !$this->errorDictionary) return;

        $serviceNames = ['materialService', 'achieveService', 'documentService', 'journalService', 'changeableService', 'foreignEventParticipantService'];

        foreach ($serviceNames as $serviceName) {
            if (isset($this->originalRepositories[$serviceName])) {
                try {
                    $reflection = new ReflectionProperty($this->errorDictionary, $serviceName);
                    $reflection->setAccessible(true);
                    $service = $reflection->getValue($this->errorDictionary);

                    if ($service && property_exists($service, 'errorsRepository')) {
                        $repoReflection = new ReflectionProperty($service, 'errorsRepository');
                        $repoReflection->setAccessible(true);
                        $repoReflection->setValue($service, $this->originalRepositories[$serviceName]);
                    }
                } catch (\Exception $e) {
                    Yii::error("Failed to restore repository in {$serviceName}: " . $e->getMessage());
                }
            }
        }

        foreach ($this->affectedTraitModels as $model) {
            $modelId = spl_object_id($model);
            if (isset($this->originalRepositories['model_' . $modelId])) {
                try {
                    $reflection = new ReflectionProperty($model, 'errorsTraitRepository');
                    $reflection->setAccessible(true);
                    $reflection->setValue($model, $this->originalRepositories['model_' . $modelId]);
                } catch (\Exception $e) {}
            }
        }

        $this->batchRepository->disableBatchMode();
        $this->originalRepositories = [];
        $this->affectedTraitModels = [];
        $this->errorDictionary = null;
        $this->preloadedErrors = [];
        $this->preloadedAmnestyErrors = [];
        $this->isActive = false;
    }

    public function flush(): array
    {
        return $this->batchRepository->flush();
    }

    public function getStats(): array
    {
        return $this->batchRepository->getPendingCount();
    }

    public function getBatchRepository(): BatchErrorsRepository
    {
        return $this->batchRepository;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}