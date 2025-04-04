<?php

namespace backend\invokables;

use backend\services\report\form\StateAssignmentReportService;
use common\helpers\common\HeaderWizard;
use common\helpers\files\FilePaths;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yii;

class ReportSALoader
{
    /**
     * @var string $templatePath часть пути к шаблону отчета
     * @var string $filename название итогового файла с отчетом
     * @var array $data данные для отчета ДОД
     */
    private string $templatePath;
    private string $filename;
    private array $data;

    public function __construct(
        string $templatePath,
        string $filename,
        array $data
    )
    {
        $this->templatePath = $templatePath;
        $this->filename = $filename;
        $this->data = $data;
    }

    public function __invoke()
    {
        $inputData = IOFactory::load(Yii::$app->basePath . FilePaths::REPORT_TEMPLATES . $this->templatePath);

        $this->setSection31($inputData, $this->data['section31']);
        $this->setSection32($inputData, $this->data['section32']);

        HeaderWizard::setExcelLoadHeaders($this->filename);
        $writer = new Xlsx($inputData);
        $writer->save('php://output');
        exit;
    }

    public function setSection31(Spreadsheet $inputData, array $data)
    {
        $inputData->getSheet(1)->setCellValue('K16', $data['technopark']['tech']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K18', $data['technopark']['tech']['personal'][StateAssignmentReportService::PARAM_PROJECTS_RATIO]);
        $inputData->getSheet(1)->setCellValue('K19', $data['technopark']['tech']['personal'][StateAssignmentReportService::PARAM_ACHIEVES_RATIO]);

        $inputData->getSheet(1)->setCellValue('K21', $data['cdntt']['tech']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K23', $data['cdntt']['tech']['personal'][StateAssignmentReportService::PARAM_ACHIEVES_RATIO]);

        $inputData->getSheet(1)->setCellValue('K25', $data['cdntt']['art']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K27', $data['cdntt']['art']['personal'][StateAssignmentReportService::PARAM_ACHIEVES_RATIO]);

        $inputData->getSheet(1)->setCellValue('K29', $data['cdntt']['social']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K31', $data['cdntt']['social']['personal'][StateAssignmentReportService::PARAM_ACHIEVES_RATIO]);

        $inputData->getSheet(1)->setCellValue('K33', $data['quantorium']['tech']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K35', $data['quantorium']['tech']['personal'][StateAssignmentReportService::PARAM_PROJECTS_RATIO]);
        $inputData->getSheet(1)->setCellValue('K36', $data['quantorium']['tech']['personal'][StateAssignmentReportService::PARAM_ACHIEVES_RATIO]);

        $inputData->getSheet(1)->setCellValue('K39', $data['mob_quant']['tech']['personal'][StateAssignmentReportService::PARAM_PROJECTS_RATIO]);

        $inputData->getSheet(1)->setCellValue('K41', $data['cod']['tech']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K43', $data['cod']['tech']['personal'][StateAssignmentReportService::PARAM_PROJECTS_RATIO]);
        $inputData->getSheet(1)->setCellValue('K44', $data['cod']['tech']['personal'][StateAssignmentReportService::PARAM_ACHIEVES_RATIO]);

        $inputData->getSheet(1)->setCellValue('K48', $data['cod']['tech']['remote'][StateAssignmentReportService::PARAM_PARTICIPANTS_RATIO]);

        $inputData->getSheet(1)->setCellValue('K49', $data['cod']['science']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K51', $data['cod']['science']['personal'][StateAssignmentReportService::PARAM_PROJECTS_RATIO]);
        $inputData->getSheet(1)->setCellValue('K52', $data['cod']['science']['personal'][StateAssignmentReportService::PARAM_ACHIEVES_RATIO]);

        $inputData->getSheet(1)->setCellValue('K54', $data['cod']['art']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K56', $data['cod']['art']['personal'][StateAssignmentReportService::PARAM_PROJECTS_RATIO]);

        $inputData->getSheet(1)->setCellValue('K58', $data['cod']['sport']['personal'][StateAssignmentReportService::PARAM_DUPLICATE]);
        $inputData->getSheet(1)->setCellValue('K60', $data['cod']['sport']['personal'][StateAssignmentReportService::PARAM_ACHIEVES_RATIO]);
    }

    public function setSection32(Spreadsheet $inputData, array $data)
    {
        $inputData->getSheet(2)->setCellValue('K8', $data['technopark']['tech']['personal']);
        $inputData->getSheet(2)->setCellValue('K9', $data['cdntt']['tech']['personal']);
        $inputData->getSheet(2)->setCellValue('K10', $data['cdntt']['art']['personal']);
        $inputData->getSheet(2)->setCellValue('K11', $data['cdntt']['social']['personal']);
        $inputData->getSheet(2)->setCellValue('K12', $data['quantorium']['tech']['personal']);
        $inputData->getSheet(2)->setCellValue('K13', $data['mob_quant']['tech']['personal']);
        $inputData->getSheet(2)->setCellValue('K14', $data['cod']['tech']['personal']);
        $inputData->getSheet(2)->setCellValue('K15', $data['cod']['tech']['remote']);
        $inputData->getSheet(2)->setCellValue('K16', $data['cod']['science']['personal']);
        $inputData->getSheet(2)->setCellValue('K17', $data['cod']['art']['personal']);
        $inputData->getSheet(2)->setCellValue('K18', $data['cod']['sport']['personal']);
    }
}