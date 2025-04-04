<?php

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class DropDownPosition extends Widget
{
    public $model; // Модель формы
    public $positions; // Данные для выпадающего списка
    public $form; // Экземпляр формы, чтобы избежать конфликта
    public $branches;
    public function run()
    {
        $this->registerAssets();
        return $this->render('DropDownPosition', [
            'model' => $this->model,
            'positions' => $this->positions,
            'form' => $this->form,
            'branches' => $this->branches,
        ]);
    }

    protected function registerAssets()
    {
        $js = <<<JS
        $('.add-dropdown-pos').click(function() {
            var newDropdown = $('#dropdown-template-pos').clone().removeAttr('id').show();
            $('#dropdown-container-pos').append(newDropdown);
        });
        
        $(document).on('click', '.remove-dropdown-pos', function() {
            $(this).closest('.dropdown-group-pos').remove();
        });
        JS;
        $this->getView()->registerJs($js);
    }
}

