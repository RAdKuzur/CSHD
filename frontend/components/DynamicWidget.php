<?php

namespace app\components;

use yii\base\Widget;
use yii\db\Exception;

class DynamicWidget extends Widget
{
    public $widgetContainer;
    public $widgetBody;
    public $widgetItem;
    public $model;
    public $formId;
    public $formFields;

    public function init()
    {
        parent::init();
        if ($this->widgetContainer === null) {
            throw new \InvalidArgumentException('widgetContainer must be set.');
        }
        if ($this->widgetBody === null) {
            throw new \InvalidArgumentException('widgetBody must be set.');
        }
        if ($this->widgetItem === null) {
            throw new \InvalidArgumentException('widgetItem must be set.');
        }
        if ($this->model === null) {
            throw new \InvalidArgumentException('model must be set.');
        }
        if ($this->formId === null) {
            throw new \InvalidArgumentException('formId must be set.');
        }
    }

    public function run()
    {
        $this->registerAssets();
        $this->script();
        return $this->render('Dynamic', [
            'model' => $this->model,
            'widgetContainer' => $this->widgetContainer,
            'widgetBody' => $this->widgetBody,
            'formId' => $this->formId,
            'formFields' => $this->formFields,
        ]);
    }

    public static function getData($modelName, $inputName, $post)
    {
        return array_key_exists($modelName, $post) && array_key_exists($inputName, $post[$modelName]) ?
            $post[$modelName][$inputName] : [];
    }

    public function registerAssets()
    {
        DynamicWidgetAsset::register($this->getView());
    }
    public function script()
    {
        $script = <<< JS
        $('#participant-select-' + 1).select2({ width: '200px' });
        let isSelected = [];        
        var index = 1; // Инициализируем с 1, так как у нас уже есть один элемент с id=item1
        $('.add-item').click(function() {
            if(index === 1){
                isSelected.push(index);
                $('#participant-select-' + 1).select2('destroy');
            }
            var container = $(this).closest('.container-items');
            var item = $('.item:last', container).clone();
            if(!$('#participant-select-' + index).data('select2')) {
                 $('#participant-select-' + index).select2({ width: '200px' });
                 $('#item-'+index).removeAttr('hidden');
            }
            index++; // Увеличиваем счетчик для нового ID
            item.attr('id', 'item-' + index); // Назначаем новый ID
            item.find('.participant-select').attr('id', 'participant-select-' + index);
            item.find('input').val('');
            container.append(item);
        });
        $('.container-items').on('click', '.remove-item', function() {
            var container = $(this).closest('.container-items');
            if (container.children('.item').length > 1) {
                console.log($(this).closest('.item').attr('id'));
                var itemId = $(this).closest('.item').attr('id');
                var number = itemId.split('-')[1];
                $('#participant-select-'+ number).select2('destroy');
                $(this).closest('.item').remove();
            }
        });
        JS;
        $this->getView()->registerJs($script);
    }
}