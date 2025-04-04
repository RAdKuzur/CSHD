<?php

namespace backend\builders;

use frontend\models\work\CertificateTemplatesWork;

class CertificateTemplatesBuilder
{
    public function query()
    {
        return CertificateTemplatesWork::find();
    }
}