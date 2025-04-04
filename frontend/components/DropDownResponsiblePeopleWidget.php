<?php
namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;

class DropDownResponsiblePeopleWidget extends Widget
{
    public $model; // Модель формы
    public $bringPeople; // Данные для выпадающего списка
    public $form; // Экземпляр формы, чтобы избежать конфликта

    public function run()
    {
        $this->registerAssets();

        return $this->render('DropDownResponsiblePeople', [
            'model' => $this->model,
            'bringPeople' => $this->bringPeople,
            'form' => $this->form,
        ]);
    }

    protected function registerAssets()
    {
        $js = <<<JS
        $('.add-dropdown-resp').click(function() {
            var newDropdown = $('#dropdown-template-resp').clone().removeAttr('id').show();
            $('#dropdown-container-resp').append(newDropdown);
        });
        
        $(document).on('click', '.remove-dropdown-resp', function() {
            $(this).closest('.dropdown-group-resp').remove();
        });
        JS;
        $this->getView()->registerJs($js);
    }
}

