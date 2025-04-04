<?php

namespace common\repositories\order;

use frontend\models\work\order\OrderMainWork;
use DomainException;
use setasign\Fpdi\PdfParser\Filter\Ascii85;

class OrderMainRepository
{

    public function get($id)
    {
        return OrderMainWork::find()->where(['id' => $id])->one();
    }
    public function delete($id)
    {
        return OrderMainWork::deleteAll(['id' => $id]);
    }
    public function save(OrderMainWork $model)
    {
        if (!$model->save()) {
            throw new DomainException('Ошибка сохранения документа. Проблемы: '.json_encode($model->getErrors()));
        }
        return $model->id;
    }

    public function getAll()
    {
        return OrderMainWork::find()->all();
    }
    public function getEqualPrefix($formNumber)
    {
        return OrderMainWork::find()
            ->where(['like', 'order_number', $formNumber.'%', false])
            ->orderBy(['order_date' => SORT_ASC])
            ->all();
    }
}