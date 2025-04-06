<?php

namespace frontend\components;

use yii\base\Widget;
use yii\helpers\Url;

class GroupParticipantWidget extends Widget
{
    public const GROUP_VIEW = '@frontend/components/views/_groups_grid';
    public const GROUP_PARTICIPANT_VIEW = '@frontend/components/views/_group-participant_grid';
    public $config;
    public $model;
    public $dataProviderGroup;
    public $dataProviderParticipant;
    public $nomenclature;
    public $transferGroups;
    public $groupCheckOption;
    public $groupParticipantOption;
    public function init()
    {
        parent::init();
        if ($this->config == NULL) {
            throw new \InvalidArgumentException('Attribute config must be set.');
        }
        if ($this->config['participantUrl'] == NULL) {
            throw new \InvalidArgumentException('Attribute participantUrl must be set.');
        }
        if ($this->config['groupUrl'] == NULL) {
            throw new \InvalidArgumentException('Attribute groupUrl must be set.');
        }
    }
    public function run(){
        $this->script();
        return $this->render('groupParticipant',
        [
            'dataProviderGroup' => $this->dataProviderGroup,
            'model' => $this->model,
            'dataProviderParticipant' => $this->dataProviderParticipant,
            'nomenclature' => $this->nomenclature,
            'transferGroups' => $this->transferGroups,
            'groupCheckOption' => $this->groupCheckOption,
            'groupParticipantOption' => $this->groupParticipantOption,
        ]);
    }
    public function script()
    {
        $this->groupParticipantScript($this->config['participantUrl']);
        $this->groupScript($this->config['groupUrl']);
    }
    public function groupScript($groupUrl){
        //группы 'order/order-training/get-group-by-branch'
        $this->getView()->registerJs("$('#branch-dropdown').on('change', function() {
        var branchId = $(this).val();
        $.ajax({
            url:'" . Url::to([$groupUrl]) . "',
            type: 'GET',
            data: { 
                branch: branchId ,
            },
            success: function(data) {
                var gridView = $('.training-group .grid-view'); 
                gridView.html(data.gridHtml); // Обновляем HTML GridView
            },
            error: function() {
                alert('Ошибка при загрузке данных.');
            }
        });
    });");
    }
    public function groupParticipantScript($participantUrl){
        //участники 'get-group-participants-by-branch'
        if($this->model->id != NULL){
            $modelId = $this->model->id;
        }
        else {
            $modelId = 0;
        }
        $this->getView()->registerJs("
        $(document).on('change', '.group-checkbox', function () {
            const checkedCheckboxes = $('.group-checkbox:checked'); 
            const groupIds = [];
            var number = $('#order-number-dropdown').val();
            var modelId = " . $modelId . ";
            checkedCheckboxes.each(function () {
                groupIds.push($(this).data('id')); // Собираем ID всех выбранных чекбоксов
            });  
            $.ajax({
                url: '" . Url::to([$participantUrl]) . "', // Укажите ваш правильный путь к контроллеру
                type: 'GET',
                data: { 
                    groupIds: JSON.stringify(groupIds), 
                    modelId: modelId, 
                    nomenclature: number, 
                }, // Отправляем массив ID
                success: function (data) {
                    var gridView = $('.training-group-participant .grid-view');
                    gridView.html(data.gridHtml); // Обновляем HTML GridView
                },
                error: function() {
                    alert('Ошибка при загрузке данных.');
                }
            });
        });");
        $this->getView()->registerJs("
        window.onload = function () {
            const checkedCheckboxes = $('.group-checkbox:checked'); 
            const groupIds = [];
            var number = $('#order-number-dropdown').val();
            var modelId = " . $modelId . ";
            checkedCheckboxes.each(function () {
                groupIds.push($(this).data('id')); // Собираем ID всех выбранных чекбоксов
            });  
            $.ajax({
                url: '" . Url::to([$participantUrl]) . "', // Укажите ваш правильный путь к контроллеру
                type: 'GET',
                data: { 
                    groupIds: JSON.stringify(groupIds), 
                    modelId: modelId, 
                    nomenclature:  number,
                }, // Отправляем массив ID
                success: function (data) {
                    var gridView = $('.training-group-participant .grid-view');
                    gridView.html(data.gridHtml); // Обновляем HTML GridView
                },
                error: function() {
                    alert('Ошибка при загрузке данных.');
                }
            });
        };
    ");
    }
}