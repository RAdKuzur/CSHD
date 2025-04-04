<?php

namespace frontend\services\order;

use app\events\act_participant\ActParticipantBranchDeleteEvent;
use app\events\act_participant\ActParticipantDeleteEvent;
use app\events\act_participant\SquadParticipantDeleteByIdEvent;
use app\events\document_order\DocumentOrderDeleteEvent;
use app\events\document_order\OrderEventGenerateDeleteEvent;
use app\events\educational\training_group\OrderTrainingGroupParticipantByIdDeleteEvent;
use app\events\educational\training_group\UpdateStatusTrainingGroupParticipantEvent;
use app\events\expire\ExpireDeleteEvent;
use app\events\foreign_event\ForeignEventDeleteEvent;
use app\events\general\OrderPeopleDeleteByIdEvent;
use app\events\team\TeamNameDeleteEvent;
use common\components\dictionaries\base\NomenclatureDictionary;
use common\repositories\act_participant\ActParticipantRepository;
use common\repositories\educational\OrderTrainingGroupParticipantRepository;
use common\repositories\event\ForeignEventRepository;
use common\repositories\expire\ExpireRepository;
use common\repositories\general\FilesRepository;
use common\repositories\general\OrderPeopleRepository;
use common\repositories\general\PeopleStampRepository;
use common\repositories\order\DocumentOrderRepository;
use common\repositories\team\TeamRepository;
use common\services\general\PeopleStampService;
use frontend\components\creators\WordCreator;
use frontend\events\educational\training_group\DeleteTrainingGroupParticipantEvent;
use frontend\events\general\FileDeleteEvent;
use frontend\models\work\educational\training_group\OrderTrainingGroupParticipantWork;
use frontend\models\work\general\FilesWork;
use frontend\models\work\general\OrderPeopleWork;
use frontend\models\work\order\DocumentOrderWork;
use common\helpers\files\filenames\DocumentOrderFileNameGenerator;
use common\helpers\files\FilesHelper;
use common\helpers\html\HtmlBuilder;
use common\services\general\files\FileService;
use frontend\events\general\FileCreateEvent;
use frontend\models\work\order\OrderTrainingWork;
use frontend\models\work\team\ActParticipantWork;
use phpseclib3\Crypt\EC\Curves\brainpoolP160r1;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\UploadedFile;

class DocumentOrderService
{

    private FileService $fileService;
    private DocumentOrderFileNameGenerator $filenameGenerator;
    private PeopleStampService $peopleStampService;
    private PeopleStampRepository $peopleStampRepository;
    private FilesRepository $filesRepository;
    private ExpireRepository $expireRepository;
    private ForeignEventRepository $foreignEventRepository;
    private TeamRepository $teamRepository;
    private ActParticipantRepository $actParticipantRepository;
    private OrderPeopleRepository $orderPeopleRepository;
    private OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository;
    private DocumentOrderRepository $orderRepository;

    public function __construct(
        FileService $fileService,
        DocumentOrderFileNameGenerator $filenameGenerator,
        PeopleStampService $peopleStampService,
        PeopleStampRepository $peopleStampRepository,
        FilesRepository $filesRepository,
        ExpireRepository $expireRepository,
        ForeignEventRepository $foreignEventRepository,
        TeamRepository $teamRepository,
        ActParticipantRepository $actParticipantRepository,
        OrderPeopleRepository $orderPeopleRepository,
        OrderTrainingGroupParticipantRepository $orderTrainingGroupParticipantRepository,
        DocumentOrderRepository $orderRepository
    )
    {
        $this->fileService = $fileService;
        $this->filenameGenerator = $filenameGenerator;
        $this->peopleStampService = $peopleStampService;
        $this->peopleStampRepository = $peopleStampRepository;
        $this->filesRepository = $filesRepository;
        $this->expireRepository = $expireRepository;
        $this->foreignEventRepository = $foreignEventRepository;
        $this->teamRepository = $teamRepository;
        $this->actParticipantRepository = $actParticipantRepository;
        $this->orderPeopleRepository = $orderPeopleRepository;
        $this->orderTrainingGroupParticipantRepository = $orderTrainingGroupParticipantRepository;
        $this->orderRepository = $orderRepository;
    }

    public function createOrderPeopleArray(array $data)
    {
        $result = [];
        foreach ($data as $item) {
            /** @var OrderPeopleWork $item */
            $result[] = ($this->peopleStampRepository->get($item->people_id))->getFullFio();
        }
        return $result;
    }

    public function getFilesInstances($model)
    {
        $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
        $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
        $model->appFiles = UploadedFile::getInstances($model, 'appFiles');
    }

    public function saveFilesFromModel($model)
    {
        if ($model->scanFile !== null) {
            $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_SCAN);
            $this->fileService->uploadFile(
                $model->scanFile,
                $filename,
                [
                    'tableName' => DocumentOrderWork::tableName(),
                    'fileType' => FilesHelper::TYPE_SCAN
                ]
            );

            $model->recordEvent(
                new FileCreateEvent(
                    $model::tableName(),
                    $model->id,
                    FilesHelper::TYPE_SCAN,
                    $filename,
                    FilesHelper::LOAD_TYPE_SINGLE
                ),
                get_class($model)
            );
        }
        if ($model->docFiles != NULL) {
            for ($i = 1; $i < count($model->docFiles) + 1; $i++) {
                $filename = $this->filenameGenerator->generateFileName($model, FilesHelper::TYPE_DOC, ['counter' => $i]);

                $this->fileService->uploadFile(
                    $model->docFiles[$i - 1],
                    $filename,
                    [
                        'tableName' => DocumentOrderWork::tableName(),
                        'fileType' => FilesHelper::TYPE_DOC
                    ]
                );

                $model->recordEvent(
                    new FileCreateEvent(
                        $model::tableName(),
                        $model->id,
                        FilesHelper::TYPE_DOC,
                        $filename,
                        FilesHelper::LOAD_TYPE_SINGLE
                    ),
                    get_class($model)
                );
            }
        }
    }

    public function getUploadedFilesTables($model)
    {
        $scanLinks = $model->getFileLinks(FilesHelper::TYPE_SCAN);
        $scanFile = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($scanLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($scanLinks), $model->id), 'fileId' => ArrayHelper::getColumn($scanLinks, 'id')])
            ]
        );

        $docLinks = $model->getFileLinks(FilesHelper::TYPE_DOC);
        $docFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($docLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($docLinks), $model->id), 'fileId' => ArrayHelper::getColumn($docLinks, 'id')])
            ]
        );

        $appLinks = $model->getFileLinks(FilesHelper::TYPE_APP);
        $appFiles = HtmlBuilder::createTableWithActionButtons(
            [
                array_merge(['Название файла'], ArrayHelper::getColumn($appLinks, 'link'))
            ],
            [
                HtmlBuilder::createButtonsArray(
                    'Удалить',
                    Url::to('delete-file'),
                    ['modelId' => array_fill(0, count($appLinks), $model->id), 'fileId' => ArrayHelper::getColumn($appLinks, 'id')])
            ]
        );

        return ['scan' => $scanFile, 'docs' => $docFiles, 'app' => $appFiles];
    }

    public function getPeopleStamps($model)
    {
        if ($model->executor_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->executor_id);
            $model->executor_id = $peopleStampId;
        }
        if ($model->signed_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->signed_id);
            $model->signed_id = $peopleStampId;
        }
        if ($model->bring_id != "") {
            $peopleStampId = $this->peopleStampService->createStampFromPeople($model->bring_id);
            $model->bring_id = $peopleStampId;
        }
    }

    public function setResponsiblePeople($responsiblePeople, $model)
    {
        foreach ($responsiblePeople as $index => $person) {
            $person = $this->peopleStampRepository->get($person);
            $responsiblePeople[$index] = $person->people_id;
        }
        $model->responsible_id = $responsiblePeople;
    }

    public function documentOrderDelete($model)
    {
        switch ($model->type) {
            case DocumentOrderWork::ORDER_MAIN:
                $this->orderMainDelete($model);
            case DocumentOrderWork::ORDER_EVENT:
                $this->orderEventDelete($model);
            case DocumentOrderWork::ORDER_TRAINING:
                $this->orderTrainingDelete($model);
        }
    }

    public function orderMainDelete(DocumentOrderWork $model)
    {
        /* @var FilesWork $file */
        $responsiblePeople = $this->orderPeopleRepository->getResponsiblePeople($model->id);
        foreach ($responsiblePeople as $person) {
            $model->recordEvent(new OrderPeopleDeleteByIdEvent($person->id), DocumentOrderWork::class);
        }
        $expires = $this->expireRepository->getExpireByActiveRegulationId($model->id);
        foreach ($expires as $expire) {
            $model->recordEvent(new ExpireDeleteEvent($expire->id), DocumentOrderWork::class);
        }
        $files = $this->filesRepository->getByDocument(DocumentOrderWork::tableName(), $model->id);
        foreach ($files as $file) {
            $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
        }
        $model->recordEvent(new DocumentOrderDeleteEvent($model->id), DocumentOrderWork::class);
    }

    public function orderEventDelete(DocumentOrderWork $model)
    {
        $responsiblePeople = $this->orderPeopleRepository->getResponsiblePeople($model->id);
        foreach ($responsiblePeople as $person) {
            $model->recordEvent(new OrderPeopleDeleteByIdEvent($person->id), DocumentOrderWork::class);
        }
        //files
        $files = $this->filesRepository->getByDocument(DocumentOrderWork::tableName(), $model->id);
        foreach ($files as $file) {
            $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
        }
        //order_event_generate
        $model->recordEvent(new OrderEventGenerateDeleteEvent($model->id), DocumentOrderWork::class);
        $event = $this->foreignEventRepository->getByDocOrderId($model->id);
        $acts = $this->actParticipantRepository->getByForeignEventIds([$event->id]);
        foreach ($acts as $act) {
            //files(act_participant)
            $files = $this->filesRepository->getByDocument(ActParticipantWork::tableName(), $act->id);
            foreach ($files as $file) {
                $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
            }
            //act_participant_branch
            $model->recordEvent(new ActParticipantBranchDeleteEvent($act->id), DocumentOrderWork::class);
            //squad_participant
            $model->recordEvent(new SquadParticipantDeleteByIdEvent($act->id), DocumentOrderWork::class);
            //act_participant
            $model->recordEvent(new ActParticipantDeleteEvent($act->id), DocumentOrderWork::class);
        }
        //team_name
        $teams = $this->teamRepository->getNamesByForeignEventId($event->id);
        foreach ($teams as $team) {
            $model->recordEvent(new TeamNameDeleteEvent($team->id), DocumentOrderWork::class);
        }
        //foreign_event
        $model->recordEvent(new ForeignEventDeleteEvent($event->id), DocumentOrderWork::class);
        $model->recordEvent(new DocumentOrderDeleteEvent($model->id), DocumentOrderWork::class);
    }

    public function orderTrainingDelete(DocumentOrderWork $model)
    {
        /* @var $orderParticipant OrderTrainingGroupParticipantWork */
        $responsiblePeople = $this->orderPeopleRepository->getResponsiblePeople($model->id);
        foreach ($responsiblePeople as $person) {
            $model->recordEvent(new OrderPeopleDeleteByIdEvent($person->id), DocumentOrderWork::class);
        }
        $status = NomenclatureDictionary::getStatus((explode("/",$model->order_number))[0]);
        $orderParticipants = $this->orderTrainingGroupParticipantRepository->getByOrderIds($model->id);
        //update & delete TrainingGroupParticipant
        foreach ($orderParticipants as $orderParticipant) {
            switch ($status) {
                case $status == NomenclatureDictionary::ORDER_ENROLL:
                    $model->recordEvent(new UpdateStatusTrainingGroupParticipantEvent($orderParticipant->training_group_participant_in_id, $status - 1), DocumentOrderWork::class);
                    $model->recordEvent(new OrderTrainingGroupParticipantByIdDeleteEvent($orderParticipant->id), DocumentOrderWork::class);
                    break;
                case $status == NomenclatureDictionary::ORDER_DEDUCT:
                    $model->recordEvent(new UpdateStatusTrainingGroupParticipantEvent($orderParticipant->training_group_participant_out_id, $status - 1), DocumentOrderWork::class);
                    $model->recordEvent(new OrderTrainingGroupParticipantByIdDeleteEvent($orderParticipant->id), DocumentOrderWork::class);
                    break;
                case $status == NomenclatureDictionary::ORDER_TRANSFER:
                    $model->recordEvent(new UpdateStatusTrainingGroupParticipantEvent($orderParticipant->training_group_participant_out_id, $status - 2), DocumentOrderWork::class);
                    $model->recordEvent(new OrderTrainingGroupParticipantByIdDeleteEvent($orderParticipant->id), DocumentOrderWork::class);
                    $model->recordEvent(new DeleteTrainingGroupParticipantEvent($orderParticipant->training_group_participant_in_id), DocumentOrderWork::class);
                    break;
            }
        }
        $files = $this->filesRepository->getByDocument(DocumentOrderWork::tableName(), $model->id);
        foreach ($files as $file) {
            $model->recordEvent(new FileDeleteEvent($file->id), DocumentOrderWork::class);
        }
        $model->recordEvent(new DocumentOrderDeleteEvent($model->id), DocumentOrderWork::class);
    }

    public function getOrdersByBranch(int $branch)
    {
        /** @var DocumentOrderWork[] $orders */
        $orders = $this->orderRepository->getAll();
        $resultOrders = [];

        foreach ($orders as $order) {
            if ($order->isTraining()) {
                // Если приказ учебный, то смотрим на номенклатуру
                if (NomenclatureDictionary::getBranchByNomenclature($order->order_number) == $branch) {
                    $resultOrders[] = $order;
                }
            }
            if ($order->isEvent()) {
                // Если приказ об участии, то пытаемся найти хотя бы одного участника с указанным отделом
                $eventIds = ArrayHelper::getColumn($this->foreignEventRepository->getByDocOrderId($order->id), 'id');
                $participants = $this->actParticipantRepository->getByForeignEventsAndBranches($eventIds, [$branch]);
                if (count($participants) > 0) {
                    $resultOrders[] = $order;
                }
            }
        }

        return $resultOrders;
    }
    public function generateOrder(DocumentOrderWork $order)
    {
        if ($order->type == DocumentOrderWork::ORDER_EVENT) {
            return WordCreator::generateOrderEvent($order->id);
        }
        else if ($order->type != DocumentOrderWork::ORDER_EVENT && NomenclatureDictionary::getStatus($order->order_number) == NomenclatureDictionary::ORDER_ENROLL) {
            return WordCreator::generateOrderTrainingEnroll($order->id);
        }
        else if ($order->type != DocumentOrderWork::ORDER_EVENT && NomenclatureDictionary::getStatus($order->order_number) == NomenclatureDictionary::ORDER_DEDUCT) {
            return WordCreator::generateOrderTrainingDeduct($order->id);
        }
        else if ($order->type != DocumentOrderWork::ORDER_EVENT && NomenclatureDictionary::getStatus($order->order_number) == NomenclatureDictionary::ORDER_TRANSFER) {
            return WordCreator::generateOrderTrainingTransfer($order->id);
        }
        else {
            return NULL;
        }
    }
}