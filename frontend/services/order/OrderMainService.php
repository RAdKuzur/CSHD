<?php

namespace frontend\services\order;

use common\repositories\dictionaries\PeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\services\general\PeopleStampService;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\order\ExpireWork;
use frontend\models\work\order\OrderMainWork;
use common\helpers\files\filenames\OrderMainFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\helpers\OrderNumberHelper;
use common\repositories\expire\ExpireRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\order\OrderMainRepository;
use common\repositories\regulation\RegulationRepository;
use common\services\general\files\FileService;
use frontend\events\expire\ExpireCreateEvent;
use frontend\events\general\FileCreateEvent;
use frontend\events\general\OrderPeopleCreateEvent;
use frontend\events\general\OrderPeopleDeleteEvent;
use frontend\models\forms\ExpireForm;
use frontend\models\work\document_in_out\DocumentInWork;
use frontend\models\work\regulation\RegulationWork;
use frontend\services\dictionaries\PeopleService;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class OrderMainService {
    private FileService $fileService;
    private OrderPeopleRepository $orderPeopleRepository;
    private RegulationRepository $regulationRepository;
    private ExpireRepository $expireRepository;
    private OrderMainRepository $orderMainRepository;
    private OrderMainFileNameGenerator $filenameGenerator;
    private PeopleStampRepository $peopleStampRepository;
    private PeopleRepository $peopleRepository;

    public function __construct(
        FileService $fileService,
        OrderMainFileNameGenerator $filenameGenerator,
        OrderMainRepository $orderMainRepository,
        OrderPeopleRepository $orderPeopleRepository,
        ExpireRepository $expireRepository,
        RegulationRepository $regulationRepository,
        PeopleStampRepository $peopleStampRepository,
        PeopleRepository $peopleRepository
    )
    {
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->fileService = $fileService;
        $this->expireRepository = $expireRepository;
        $this->regulationRepository = $regulationRepository;
        $this->orderMainRepository = $orderMainRepository;
        $this->filenameGenerator = $filenameGenerator;
        $this->peopleStampRepository = $peopleStampRepository;
        $this->peopleRepository = $peopleRepository;
    }
    public function createChangedDocumentsArray(array $data)
    {
        $result = [];
        foreach ($data as $item) {
            /** @var ExpireWork $item */

            if ($item->expire_order_id != NULL) {
                /** @var OrderMainWork $model */
                $model = $this->orderMainRepository->get($item->expire_order_id);
                $result[] = $model->order_name.'  ('.$item->getStatus().')';
            }
            if ($item->expire_regulation_id != NULL) {
                /** @var RegulationWork $model */
                $model = $this->regulationRepository->get($item->expire_regulation_id);
                $result[] =  $model->name.'  ('.$item->getStatus().')';
            }

        }
        return $result;
    }
    public function getChangedDocumentsTable(int $modelId)
    {
        $expires = $this->expireRepository->getExpireByActiveRegulationId($modelId);

        return HtmlBuilder::createTableWithActionButtons(
                    [
                        array_merge(['Тип документа'], ArrayHelper::getColumn($expires, 'type')),
                        array_merge(['Номер документа'], ArrayHelper::getColumn($expires, 'number')),
                        array_merge(['Статус'], ArrayHelper::getColumn($expires, 'status'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-document'),
                    ['id' => ArrayHelper::getColumn($expires, 'id'), 'modelId' => array_fill(0, count($expires), $modelId)])
            ]
        );
    }
    public function addExpireEvent($expires, OrderMainWork $model) {
        /* @var ExpireForm $expire */
        foreach ($expires as $expire) {
            $expire = ExpireForm::attachAttributes($model->id, $expire["expireRegulationId"], $expire["expireOrderId"], $expire["docType"], $expire["expireType"]);
            if (($expire->expireOrderId != "" xor $expire->expireRegulationId != "")
                && $this->expireRepository->checkUnique(
                    $model->id,
                    $expire->expireRegulationId,
                    $expire->expireOrderId,
                    $expire->docType,
                    $expire->expireType)
            )
            {
                $model->recordEvent(new ExpireCreateEvent(
                    $model->id, $expire->expireRegulationId, $expire->expireOrderId, $expire->docType, $expire->expireType
                ), ExpireWork::class);
            }
        }
    }
    public function deleteOrderPeopleEvent($respPeople, $model){
        if (is_array($respPeople)) {
            $respPeople = array_unique($respPeople);
            foreach ($respPeople as $person) {
                if ($person != NULL) {
                    if (!$this->orderPeopleRepository->checkUnique($person, $model->id)) {
                        $model->recordEvent(new OrderPeopleDeleteEvent($person, $model->id), OrderPeopleWork::class);
                    }
                }
            }
        }
    }
    public function createArrayNumber($records, $array_number)
    {
        foreach ($records as $record) {
            /* @var OrderMainWork $record */
            if($record->order_postfix == NULL) {
                $array_number[] = [
                    $record->order_date,
                    $record->order_number,
                    $record->order_number
                ];
            }
            else {
                $array_number[] = [
                    $record->order_date,
                    $record->order_number,
                    $record->order_number . '/' . $record->order_postfix
                ];
            }
        }
        return $array_number;
    }
    public function createOrderNumber($array_number, $downItem, $equalItem , $upItem, $isPostfix, $index, $formNumber, $model_date)
    {
        for ($i = 0; $i < count($array_number); $i++) {
            $item = $array_number[$i];
            if ($item[0] < $model_date) {
                $downItem = $item;
            }
            if ($item[0] == $model_date) {
                $equalItem[] = $item;
            }
            if ($item[0] > $model_date) {
                $upItem = $item;
                break;
            }
        }
        OrderNumberHelper::sortArrayByOrderNumber($equalItem);
        if($equalItem != NULL) {
            $downItem = $equalItem[count($equalItem) - 1];
        }
        $newNumber = $downItem[2];
        if($downItem != NULL) {
            while (OrderNumberHelper::findByNumberPostfix($array_number, $newNumber)) {
                $parts = OrderNumberHelper::splitString($newNumber);
                $number = $parts[0];
                for ($i = 1; $i < count($parts) - 1; $i++) {
                    $number = $number . '/' . (string)$parts[$i];
                }
                if (($upItem[2] > $number . '/' . (string)((int)$parts[count($parts) - 1] + 1)
                        && !OrderNumberHelper::findByNumberPostfix($array_number, $number . '/' . (string)((int)$parts[count($parts) - 1] + 1))) || $upItem == NULL) {
                    $number = $number . '/' . (string)((int)$parts[count($parts) - 1] + 1);
                    $newNumber = $number;
                    $isPostfix = 0;
                    break;
                } else {
                    $isPostfix = 1;
                    if ($upItem[2] > $number . '/' . (string)$index && OrderNumberHelper::findByNumberPostfix($array_number, $number . '/' . (string)$index)) {
                        $number = $number . '/' . (string)$index;
                    } else {
                        $index = 1;
                        $number = $newNumber . '/' . '1';
                    }
                }
                $newNumber = $number;
                $index++;
            }
            if($isPostfix == 0) {
                $order_number = $newNumber;
                $order_postfix = NULL;
            }
            else {
                $parts = OrderNumberHelper::splitString($newNumber);
                $number = $parts[0];
                for ($i = 1; $i < count($parts) - 1; $i++) {
                    $number = $number . '/' . (string)$parts[$i];
                }
                $order_number = $number;
                $order_postfix = $parts[count($parts) - 1];
            }
        }
        else {
            $order_number = $formNumber;
            $order_postfix = NULL;
        }
        return ['number' => $order_number, 'postfix' => $order_postfix];
    }
    public function createReserve(OrderMainWork $model)
    {
        $model->createReserve();
    }
}