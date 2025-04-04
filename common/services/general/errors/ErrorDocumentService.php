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
use common\repositories\general\ErrorsRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\order\OrderEventGenerateRepository;
use common\repositories\order\OrderMainRepository;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\document_in_out\InOutDocumentsWork;
use frontend\models\work\order\DocumentOrderWork;
use Yii;

class ErrorDocumentService
{
    private ErrorsRepository $errorsRepository;
    private DocumentOrderRepository $orderRepository;
    private OrderTrainingGroupParticipantRepository $orderParticipantRepository;
    private OrderEventGenerateRepository $eventGenerateRepository;
    private ForeignEventRepository $foreignEventRepository;
    private DocumentInRepository $documentInRepository;
    private DocumentOutRepository $documentOutRepository;
    private InOutDocumentsRepository $inOutDocumentsRepository;

    public function __construct(
        ErrorsRepository $errorsRepository,
        DocumentOrderRepository $orderRepository,
        OrderTrainingGroupParticipantRepository $orderParticipantRepository,
        OrderEventGenerateRepository $eventGenerateRepository,
        ForeignEventRepository $foreignEventRepository,
        DocumentInRepository $documentInRepository,
        DocumentOutRepository $documentOutRepository,
        InOutDocumentsRepository $inOutDocumentsRepository
    )
    {
        $this->errorsRepository = $errorsRepository;
        $this->orderRepository = $orderRepository;
        $this->orderParticipantRepository = $orderParticipantRepository;
        $this->eventGenerateRepository = $eventGenerateRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->documentInRepository = $documentInRepository;
        $this->documentOutRepository = $documentOutRepository;
        $this->inOutDocumentsRepository = $inOutDocumentsRepository;
    }

    // Проверка на отсутствие скана
    public function makeDocument_001($rowId)
    {
        $order = $this->orderRepository->get($rowId);
        if (count($order->getFileLinks(FilesHelper::TYPE_SCAN)) == 0) {
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

    public function fixDocument_001($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $order = $this->orderRepository->get($error->table_row_id);
        if (count($order->getFileLinks(FilesHelper::TYPE_SCAN)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    public function makeDocument_002($rowId)
    {
        // deprecated
    }

    public function fixDocument_002($errorId)
    {
        // deprecated
    }

    // Проверка на отсутствие ключевых слов
    public function makeDocument_003($rowId)
    {
        $order = $this->orderRepository->get($rowId);
        if (is_null($order->key_words) || strlen($order->key_words) == 0) {
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

    public function fixDocument_003($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $order = $this->orderRepository->get($error->table_row_id);
        if (!(is_null($order->key_words) || strlen($order->key_words) == 0)) {
            $this->errorsRepository->delete($error);
        }
    }

    public function makeDocument_004($rowId)
    {
        // deprecated
    }

    public function fixDocument_004($errorId)
    {
        // deprecated
    }

    // Проверка на наличие обучающихся, прикрепленных к приказу
    public function makeDocument_005($rowId)
    {
        $orderParticipant = $this->orderParticipantRepository->getByOrderIds($rowId);
        if (count($orderParticipant) == 0) {
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

    public function fixDocument_005($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $orderParticipant = $this->orderParticipantRepository->getByOrderIds($error->table_row_id);
        if (count($orderParticipant) != 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на связанное мероприятия (наличие информации для генерации мероприятия)
    public function makeDocument_006($rowId)
    {
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($rowId);
        if (!$foreignEvent) {
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

    public function fixDocument_006($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $foreignEvent = $this->foreignEventRepository->getByDocOrderId($error->table_row_id);
        if ($foreignEvent) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на наличие данных для генерации документа приказа
    public function makeDocument_007($rowId)
    {
        $generateData = $this->eventGenerateRepository->getByOrderId($rowId);
        if (!$generateData) {
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

    public function fixDocument_007($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $generateData = $this->eventGenerateRepository->getByOrderId($error->table_row_id);
        if ($generateData) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие скана в исходящем письме
    public function makeDocument_008($rowId)
    {
        /** @var DocumentOutWork $doc */
        $doc = $this->documentOutRepository->get($rowId);
        if (count($doc->getFileLinks(FilesHelper::TYPE_SCAN)) == 0) {
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

    public function fixDocument_008($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $doc = $this->documentOutRepository->get($error->table_row_id);
        if (count($doc->getFileLinks(FilesHelper::TYPE_SCAN)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие редактируемого документа в исходящем письме
    public function makeDocument_009($rowId)
    {
        /** @var DocumentOutWork $doc */
        $doc = $this->documentOutRepository->get($rowId);
        if (count($doc->getFileLinks(FilesHelper::TYPE_DOC)) == 0) {
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

    public function fixDocument_009($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $doc = $this->documentOutRepository->get($error->table_row_id);
        if (count($doc->getFileLinks(FilesHelper::TYPE_DOC)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие ключевых слов в исходящем письме
    public function makeDocument_010($rowId)
    {
        /** @var DocumentOutWork $doc */
        $doc = $this->documentOutRepository->get($rowId);
        if (is_null($doc->key_words) || strlen($doc->key_words) == 0) {
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

    public function fixDocument_010($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var DocumentOutWork $doc */
        $error = $this->errorsRepository->get($errorId);
        $doc = $this->documentOutRepository->get($error->table_row_id);
        if (!(is_null($doc->key_words) || strlen($doc->key_words) == 0)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие скана во входящем письме
    public function makeDocument_011($rowId)
    {
        /** @var DocumentInWork $doc */
        $doc = $this->documentInRepository->get($rowId);
        if (count($doc->getFileLinks(FilesHelper::TYPE_SCAN)) == 0) {
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

    public function fixDocument_011($errorId)
    {
        /** @var ErrorsWork $error */
        $error = $this->errorsRepository->get($errorId);
        $doc = $this->documentInRepository->get($error->table_row_id);
        if (count($doc->getFileLinks(FilesHelper::TYPE_SCAN)) > 0) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на отсутствие ключевых слов во входящем письме
    public function makeDocument_012($rowId)
    {
        /** @var DocumentInWork $doc */
        $doc = $this->documentInRepository->get($rowId);
        if (is_null($doc->key_words) || strlen($doc->key_words) == 0) {
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

    public function fixDocument_012($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var DocumentInWork $doc */
        $error = $this->errorsRepository->get($errorId);
        $doc = $this->documentInRepository->get($error->table_row_id);
        if (!(is_null($doc->key_words) || strlen($doc->key_words) == 0)) {
            $this->errorsRepository->delete($error);
        }
    }

    // Проверка на своевременность и наличие ответа на входящее письмо
    public function makeDocument_013($rowId)
    {
        /** @var DocumentInWork $doc */
        $doc = $this->documentInRepository->get($rowId);
        if ($doc->isNeedAnswer()) {
            /** @var InOutDocumentsWork $answer */
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

    public function fixDocument_013($errorId)
    {
        /** @var ErrorsWork $error */
        /** @var DocumentInWork $doc */
        $error = $this->errorsRepository->get($errorId);
        $doc = $this->documentInRepository->get($error->table_row_id);
        if ($doc->isNeedAnswer()) {
            /** @var InOutDocumentsWork $answer */
            $answer = $this->inOutDocumentsRepository->getByDocumentInId($error->table_row_id);
            if (!($answer && is_null($answer->document_out_id))) {
                $this->errorsRepository->delete($error);
            }
        }
    }
}