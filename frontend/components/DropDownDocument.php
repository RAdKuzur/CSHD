<?php

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class DropDownDocument extends Widget
{
    public $model; // Модель формы
    public $bringPeople; // Данные для выпадающего списка
    public $form; // Экземпляр формы, чтобы избежать конфликта

    public function run()
    {
        return $this->render('DropDownDocument', [
            'model' => $this->model,
            'bringPeople' => $this->bringPeople,
            'form' => $this->form,
        ]);
    }

    protected function registerAssets()
    {
        $js = <<<JS
        $('.add-dropdown-doc-ch').click(function() {
            var newDropdown = $('#dropdown-template-doc-ch').clone().removeAttr('id').show();
            $('#dropdown-container-doc-ch').append(newDropdown);
        });
        
        $(document).on('click', '.remove-dropdown-doc-ch', function() {
            $(this).closest('.dropdown-group-doc-ch').remove();
        });
        JS;
        $this->getView()->registerJs($js);
    }
}

