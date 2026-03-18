<?php

namespace frontend\models\search;

use common\models\work\ErrorsWork;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use Yii;

class SearchErrors extends Model
{
    public $error_code;
    public $error_description;
    public $create_date_from;
    public $create_date_to;
    public $branch;
    public $entity_name;

    public function rules()
    {
        return [
            [['error_code', 'error_description', 'branch', 'entity_name'], 'safe'],
            [['create_date_from', 'create_date_to'], 'safe'],
        ];
    }

    public function search($params, $errors)
    {
        $this->load($params);
        
        // Фильтруем массив ошибок
        $filteredErrors = array_filter($errors, function($error) {
            // Получаем данные об ошибке
            $errorData = Yii::$app->errors->get($error->error);
            
            // Фильтр по коду ошибки
            if (!empty($this->error_code) && strpos($errorData->code, $this->error_code) === false) {
                return false;
            }
            
            // Фильтр по описанию
            if (!empty($this->error_description) && strpos($errorData->description, $this->error_description) === false) {
                return false;
            }
            
            // Фильтр по отделу
            if (!empty($this->branch) && $error->branch != $this->branch) {
                return false;
            }
            
            // Фильтр по дате (с)
            if (!empty($this->create_date_from)) {
                $dateFrom = strtotime($this->create_date_from);
                $errorDate = strtotime(explode(' ', $error->create_datetime)[0]);
                if ($errorDate < $dateFrom) {
                    return false;
                }
            }
            
            // Фильтр по дате (по)
            if (!empty($this->create_date_to)) {
                $dateTo = strtotime($this->create_date_to);
                $errorDate = strtotime(explode(' ', $error->create_datetime)[0]);
                if ($errorDate > $dateTo) {
                    return false;
                }
            }

            // Фильтр по названию сущности
            if (!empty($this->entity_name) && strpos($error->getEntityName(), $this->entity_name) === false) {
                return false;
            }
            
            return true;
        });
        
        return $filteredErrors;
    }
}