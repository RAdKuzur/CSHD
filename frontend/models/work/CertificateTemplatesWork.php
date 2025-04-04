<?php

namespace frontend\models\work;

use common\models\scaffold\CertificateTemplates;

class CertificateTemplatesWork extends CertificateTemplates
{
    public function rules()
    {
        return [
            [['name'], 'string'],
        ];
    }
}
