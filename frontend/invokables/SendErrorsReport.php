<?php

namespace frontend\invokables;

use common\helpers\creators\ExcelCreator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Yii;

class SendErrorsReport
{
    private Spreadsheet $spreadsheet;
    private string $reportName;
    private string $email;
    private string $reportTitle;

    public function __construct(
        Spreadsheet $spreadsheet,
        string $reportName,
        string $email,
        string $reportTitle
    ) {
        $this->spreadsheet = $spreadsheet;
        $this->reportName = $reportName;
        $this->email = $email;
        $this->reportTitle = $reportTitle;
    }

    public function __invoke(): bool
    {
        $tempFile = Yii::getAlias('@runtime') . '/' . $this->reportName . '_' . date('d.m.Y') . '.xlsx';

        try {
            ExcelCreator::saveSpreadsheet($this->spreadsheet, $tempFile);

            if (!file_exists($tempFile)) {
                Yii::error("File not created: {$tempFile}", 'mail');
                return false;
            }

            $text = "Еженедельный отчёт об ошибках в системе ЦСХД.<br><br>"
                . "Файл содержит {$this->reportTitle}.<br><br>"
                . "<b>Если в отчете нет определённого раздела, значит в нём нет ошибок данного типа.</b><br><br>"
                . "Пожалуйста, обратите внимание: это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него.";

            $result = Yii::$app->mailer->compose()
                ->setFrom([
                    Yii::$app->params['reportSenderEmail'] ?? 'noreply@schooltech.ru' => 'ЦСХД'
                ])
                ->setTo($this->email)
                ->setSubject('Еженедельный отчёт об ошибках')
                ->setHtmlBody($text)
                ->attach($tempFile)
                ->send();

            if ($result) {
                Yii::info("Mail sent to {$this->email}", 'mail');
            } else {
                Yii::error("Mail sending failed to {$this->email}", 'mail');
            }

            return $result;

        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'mail');
            return false;

        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }
}