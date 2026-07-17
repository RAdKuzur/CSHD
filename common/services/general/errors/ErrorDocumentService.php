<?php

namespace common\services\general\errors;

use common\components\dictionaries\base\ErrorDictionary;
use common\helpers\files\FilesHelper;
use common\models\work\ErrorsWork;
use common\repositories\document_in_out\DocumentInRepository;
use common\repositories\document_in_out\DocumentOutRepository;
use common\repositories\document_in_out\InOutDocumentsRepository;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\general\ErrorsRepositoryInterface;
use common\repositories\general\FilesRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\order\OrderEventGenerateRepository;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\order\DocumentOrderWork;
use Yii;

class ErrorDocumentService
{
    private ErrorsRepositoryInterface $errorsRepository;
    private DocumentOrderRepository $orderRepository;
    private OrderTrainingGroupParticipantRepository $orderParticipantRepository;
    private OrderEventGenerateRepository $eventGenerateRepository;
    private ForeignEventRepository $foreignEventRepository;
    private DocumentInRepository $documentInRepository;
    private DocumentOutRepository $documentOutRepository;
    private InOutDocumentsRepository $inOutDocumentsRepository;
    private FilesRepository $filesRepository;

    public function __construct(
        ErrorsRepositoryInterface $errorsRepository,
        DocumentOrderRepository $orderRepository,
        OrderTrainingGroupParticipantRepository $orderParticipantRepository,
        OrderEventGenerateRepository $eventGenerateRepository,
        ForeignEventRepository $foreignEventRepository,
        DocumentInRepository $documentInRepository,
        DocumentOutRepository $documentOutRepository,
        InOutDocumentsRepository $inOutDocumentsRepository,
        FilesRepository $filesRepository
    ) {
        $this->errorsRepository = $errorsRepository;
        $this->orderRepository = $orderRepository;
        $this->orderParticipantRepository = $orderParticipantRepository;
        $this->eventGenerateRepository = $eventGenerateRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->documentInRepository = $documentInRepository;
        $this->documentOutRepository = $documentOutRepository;
        $this->inOutDocumentsRepository = $inOutDocumentsRepository;
        $this->filesRepository = $filesRepository;
    }

    public function setErrorsRepository(ErrorsRepositoryInterface $repository): void
    {
        $this->errorsRepository = $repository;
    }

    /**
     * DataFetch для DOCUMENT_001, DOCUMENT_002, DOCUMENT_003
     * Предзагружает все приказы и их файлы
     */
    public function fetchDataForDocumentOrders(array $rowIds,string $tablename): array
    {
        // Загружаем все приказы одним запросом
        $orders = $this->orderRepository->getByIds($rowIds);
        $fileTypes = [FilesHelper::TYPE_DOC, FilesHelper::TYPE_SCAN];
        $files = $this->filesRepository->getAll($tablename,$fileTypes);

        // Группируем файлы по table_row_id и file_type
        $filesMap = [];
        foreach ($files as $file) {
            $rowId = $file->table_row_id;
            $type = $file->file_type;

            if (!isset($filesMap[$rowId])) {
                $filesMap[$rowId] = [
                    FilesHelper::TYPE_DOC => 0,
                    FilesHelper::TYPE_SCAN => 0,
                ];
            }
            $filesMap[$rowId][$type]++;
        }

        $orderData = [];
        foreach ($orders as $order) {
            $rowFiles = $filesMap[$order->id] ?? [
                FilesHelper::TYPE_DOC => 0,
                FilesHelper::TYPE_SCAN => 0,
            ];

            $orderData[$order->id] = [
                'has_scan' => $rowFiles[FilesHelper::TYPE_SCAN] > 0,
                'has_doc' => $rowFiles[FilesHelper::TYPE_DOC] > 0,
                'has_keywords' => !(is_null($order->key_words) || strlen($order->key_words) == 0),
                'model' => $order,
            ];
        }

        return $orderData;
    }

    /**
     * DataFetch для DOCUMENT_005
     * Предзагружает количество участников для всех приказов
     */
    public function fetchDataForDocument_005(array $rowIds): array
    {
        // Один запрос с подсчетом для всех приказов
        $counts = $this->orderParticipantRepository->getCountsByOrderIds($rowIds);
        return $counts;
    }

    /**
     * DataFetch для DOCUMENT_006
     * Предзагружает связи с мероприятиями
     */
    public function fetchDataForDocument_006(array $rowIds): array
    {
        // Один запрос для всех приказов
        $events = $this->foreignEventRepository->getByDocOrderIds($rowIds);

        $eventMap = [];
        foreach ($events as $event) {
            $eventMap[$event->doc_order_id] = true;
        }

        return $eventMap;
    }

    /**
     * DataFetch для DOCUMENT_007
     * Предзагружает данные генерации
     */
    public function fetchDataForDocument_007(array $rowIds): array
    {
        $generateData = $this->eventGenerateRepository->getByOrderIds($rowIds);

        $generateMap = [];
        foreach ($generateData as $data) {
            $generateMap[$data->order_id] = true;
        }

        return $generateMap;
    }

    /**
     * DataFetch для DOCUMENT_008, DOCUMENT_009, DOCUMENT_010
     * Предзагружает исходящие документы и их файлы
     */
    public function fetchDataForDocumentOuts(array $rowIds): array
    {
        $docs = $this->documentOutRepository->getByIds($rowIds);
        $fileTypes = [FilesHelper::TYPE_DOC, FilesHelper::TYPE_SCAN];
        $files = $this->filesRepository->getAll(DocumentOutWork::tableName(), $fileTypes);

        // Группируем файлы по table_row_id и file_type
        $filesMap = [];
        foreach ($files as $file) {
            $rowId = $file->table_row_id;
            $type = $file->file_type;

            if (!isset($filesMap[$rowId])) {
                $filesMap[$rowId] = [
                    FilesHelper::TYPE_DOC => 0,
                    FilesHelper::TYPE_SCAN => 0,
                ];
            }
            $filesMap[$rowId][$type]++;
        }

        $docData = [];
        foreach ($docs as $doc) {
            $rowFiles = $filesMap[$doc->id] ?? [
                FilesHelper::TYPE_DOC => 0,
                FilesHelper::TYPE_SCAN => 0,
            ];

            $docData[$doc->id] = [
                'has_scan' => $rowFiles[FilesHelper::TYPE_SCAN] > 0,
                'has_doc' => $rowFiles[FilesHelper::TYPE_DOC] > 0,
                'has_keywords' => !(is_null($doc->key_words) || strlen($doc->key_words) == 0),
                'model' => $doc,
            ];
        }

        return $docData;
    }

    /**
     * DataFetch для DOCUMENT_011, DOCUMENT_012
     * Предзагружает входящие документы и их файлы
     */
    public function fetchDataForDocumentIns(array $rowIds): array
    {
        $docs = $this->documentInRepository->getByIds($rowIds);

        // Для DocumentIn нужен только SCAN (DOCUMENT_011)
        $fileTypes = [FilesHelper::TYPE_SCAN];
        $files = $this->filesRepository->getAll(DocumentInWork::tableName(), $fileTypes);

        // Группируем файлы по table_row_id
        $filesMap = [];
        foreach ($files as $file) {
            $rowId = $file->table_row_id;
            $filesMap[$rowId] = ($filesMap[$rowId] ?? 0) + 1;
        }

        $docData = [];
        foreach ($docs as $doc) {
            $docData[$doc->id] = [
                'has_scan' => ($filesMap[$doc->id] ?? 0) > 0,
                'has_keywords' => !(is_null($doc->key_words) || strlen($doc->key_words) == 0),
                'is_need_answer' => $doc->isNeedAnswer(),
                'model' => $doc,
            ];
        }

        return $docData;
    }

    /**
     * DataFetch для DOCUMENT_013
     * Предзагружает связи входящих и исходящих
     */
    public function fetchDataForDocument_013(array $rowIds): array
    {
        $links = $this->inOutDocumentsRepository->getByDocumentInIds($rowIds);

        $linkMap = [];
        foreach ($links as $link) {
            $linkMap[$link->document_in_id] = [
                'exists' => true,
                'has_out' => !is_null($link->document_out_id),
            ];
        }

        return $linkMap;
    }

    // ========== ОБНОВЛЕННЫЕ МЕТОДЫ MAKE/FIX ==========

    // DOCUMENT_001
    public function makeDocument_001($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasScan = $preloadedData[$rowId]['has_scan'] ?? false;
        } else {
            $order = $this->orderRepository->get($rowId);
            $hasScan = count($order->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
        }

        if (!$hasScan) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_001,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_001)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_001($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasScan = $preloadedData[$error->table_row_id]['has_scan'] ?? false;
        } else {
            $order = $this->orderRepository->get($error->table_row_id);
            $hasScan = count($order->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
        }

        if ($hasScan) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_002
    public function makeDocument_002($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasDoc = $preloadedData[$rowId]['has_doc'] ?? false;
        } else {
            $order = $this->orderRepository->get($rowId);
            $hasDoc = count($order->getFileLinks(FilesHelper::TYPE_DOC)) > 0;
        }

        if (!$hasDoc) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_002,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_002)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_002($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasDoc = $preloadedData[$error->table_row_id]['has_doc'] ?? false;
        } else {
            $order = $this->orderRepository->get($error->table_row_id);
            $hasDoc = count($order->getFileLinks(FilesHelper::TYPE_DOC)) > 0;
        }

        if ($hasDoc) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_003
    public function makeDocument_003($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasKeywords = $preloadedData[$rowId]['has_keywords'] ?? false;
        } else {
            $order = $this->orderRepository->get($rowId);
            $hasKeywords = !(is_null($order->key_words) || strlen($order->key_words) == 0);
        }

        if (!$hasKeywords) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_003,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_003)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_003($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasKeywords = $preloadedData[$error->table_row_id]['has_keywords'] ?? false;
        } else {
            $order = $this->orderRepository->get($error->table_row_id);
            $hasKeywords = !(is_null($order->key_words) || strlen($order->key_words) == 0);
        }

        if ($hasKeywords) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_005
    public function makeDocument_005($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $count = $preloadedData[$rowId] ?? 0;
        } else {
            $orderParticipant = $this->orderParticipantRepository->getByOrderIds($rowId);
            $count = count($orderParticipant);
        }

        if ($count == 0) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_005,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_005)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_005($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $count = $preloadedData[$error->table_row_id] ?? 0;
        } else {
            $orderParticipant = $this->orderParticipantRepository->getByOrderIds($error->table_row_id);
            $count = count($orderParticipant);
        }

        if ($count != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_006
    public function makeDocument_006($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasEvent = isset($preloadedData[$rowId]);
        } else {
            $foreignEvent = $this->foreignEventRepository->getByDocOrderId($rowId);
            $hasEvent = (bool)$foreignEvent;
        }

        if (!$hasEvent) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_006,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_006)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_006($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasEvent = isset($preloadedData[$error->table_row_id]);
        } else {
            $foreignEvent = $this->foreignEventRepository->getByDocOrderId($error->table_row_id);
            $hasEvent = (bool)$foreignEvent;
        }

        if ($hasEvent) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_007
    public function makeDocument_007($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasGenerate = isset($preloadedData[$rowId]);
        } else {
            $generateData = $this->eventGenerateRepository->getByOrderId($rowId);
            $hasGenerate = (bool)$generateData;
        }

        if (!$hasGenerate) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_007,
                    DocumentOrderWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_007)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_007($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasGenerate = isset($preloadedData[$error->table_row_id]);
        } else {
            $generateData = $this->eventGenerateRepository->getByOrderId($error->table_row_id);
            $hasGenerate = (bool)$generateData;
        }

        if ($hasGenerate) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_008
    public function makeDocument_008($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasScan = $preloadedData[$rowId]['has_scan'] ?? false;
        } else {
            $doc = $this->documentOutRepository->get($rowId);
            $hasScan = count($doc->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
        }

        if (!$hasScan) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_008,
                    DocumentOutWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_008)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_008($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasScan = $preloadedData[$error->table_row_id]['has_scan'] ?? false;
        } else {
            $doc = $this->documentOutRepository->get($error->table_row_id);
            $hasScan = count($doc->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
        }

        if ($hasScan) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_009
    public function makeDocument_009($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasDoc = $preloadedData[$rowId]['has_doc'] ?? false;
        } else {
            $doc = $this->documentOutRepository->get($rowId);
            $hasDoc = count($doc->getFileLinks(FilesHelper::TYPE_DOC)) > 0;
        }

        if (!$hasDoc) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_009,
                    DocumentOutWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_009)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_009($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasDoc = $preloadedData[$error->table_row_id]['has_doc'] ?? false;
        } else {
            $doc = $this->documentOutRepository->get($error->table_row_id);
            $hasDoc = count($doc->getFileLinks(FilesHelper::TYPE_DOC)) > 0;
        }

        if ($hasDoc) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_010
    public function makeDocument_010($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasKeywords = $preloadedData[$rowId]['has_keywords'] ?? false;
        } else {
            $doc = $this->documentOutRepository->get($rowId);
            $hasKeywords = !(is_null($doc->key_words) || strlen($doc->key_words) == 0);
        }

        if (!$hasKeywords) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_010,
                    DocumentOutWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_010)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_010($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasKeywords = $preloadedData[$error->table_row_id]['has_keywords'] ?? false;
        } else {
            $doc = $this->documentOutRepository->get($error->table_row_id);
            $hasKeywords = !(is_null($doc->key_words) || strlen($doc->key_words) == 0);
        }

        if ($hasKeywords) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_011
    public function makeDocument_011($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasScan = $preloadedData[$rowId]['has_scan'] ?? false;
        } else {
            $doc = $this->documentInRepository->get($rowId);
            $hasScan = count($doc->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
        }

        if (!$hasScan) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_011,
                    DocumentInWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_011)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_011($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasScan = $preloadedData[$error->table_row_id]['has_scan'] ?? false;
        } else {
            $doc = $this->documentInRepository->get($error->table_row_id);
            $hasScan = count($doc->getFileLinks(FilesHelper::TYPE_SCAN)) > 0;
        }

        if ($hasScan) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_012
    public function makeDocument_012($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $hasKeywords = $preloadedData[$rowId]['has_keywords'] ?? false;
        } else {
            $doc = $this->documentInRepository->get($rowId);
            $hasKeywords = !(is_null($doc->key_words) || strlen($doc->key_words) == 0);
        }

        if (!$hasKeywords) {
            $this->errorsRepository->save(
                ErrorsWork::fill(
                    ErrorDictionary::DOCUMENT_012,
                    DocumentInWork::tableName(),
                    $rowId,
                    Yii::$app->errors->get(ErrorDictionary::DOCUMENT_012)->getErrorState()
                )
            );
        }
    }

    public function fixDocument_012($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $hasKeywords = $preloadedData[$error->table_row_id]['has_keywords'] ?? false;
        } else {
            $doc = $this->documentInRepository->get($error->table_row_id);
            $hasKeywords = !(is_null($doc->key_words) || strlen($doc->key_words) == 0);
        }

        if ($hasKeywords) {
            $this->errorsRepository->delete($error);
        }
    }

    // DOCUMENT_013
    public function makeDocument_013($rowId, $preloadedData = null)
    {
        if ($preloadedData !== null) {
            $docData = $preloadedData[$rowId] ?? null;
            if ($docData && $docData['is_need_answer']) {
                $linkData = $preloadedData[$rowId] ?? null;
                if ($linkData && $linkData['exists'] && !$linkData['has_out']) {
                    $this->errorsRepository->save(
                        ErrorsWork::fill(
                            ErrorDictionary::DOCUMENT_013,
                            DocumentInWork::tableName(),
                            $rowId,
                            Yii::$app->errors->get(ErrorDictionary::DOCUMENT_013)->getErrorState()
                        )
                    );
                }
            }
        } else {
            $doc = $this->documentInRepository->get($rowId);
            if ($doc->isNeedAnswer()) {
                $answer = $this->inOutDocumentsRepository->getByDocumentInId($rowId);
                if ($answer && is_null($answer->document_out_id)) {
                    $this->errorsRepository->save(
                        ErrorsWork::fill(
                            ErrorDictionary::DOCUMENT_013,
                            DocumentInWork::tableName(),
                            $rowId,
                            Yii::$app->errors->get(ErrorDictionary::DOCUMENT_013)->getErrorState()
                        )
                    );
                }
            }
        }
    }

    public function fixDocument_013($errorId, $preloadedData = null)
    {
        $error = $this->errorsRepository->get($errorId);

        if ($preloadedData !== null) {
            $linkData = $preloadedData[$error->table_row_id] ?? null;
            if (!$linkData || !$linkData['exists'] || $linkData['has_out']) {
                $this->errorsRepository->delete($error);
            }
        } else {
            $doc = $this->documentInRepository->get($error->table_row_id);
            $inOutDocs = $this->inOutDocumentsRepository->getByDocumentInId($doc->id);
            if (is_null($inOutDocs) || !is_null($inOutDocs->document_out_id)) {
                $this->errorsRepository->delete($error);
            }
        }
    }
}