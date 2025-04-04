<?php

namespace common\helpers\files;

class FilePaths
{
    public const BASE_FILEPATH = '/upload/files';
    public const TEMPLATE_FILEPATH = '/upload/templates';
    public const TEMP_FILEPATH = self::BASE_FILEPATH . '/temp';
    public const EXAMPLE_FILEPATH = '/upload/example';
    public const EXAMPLE_UTP = self::EXAMPLE_FILEPATH . '/utp-example.xlsx';

    public const SVG_FILE_DOWNLOAD = 'svg/download-file.svg';
    public const SVG_FILE_NO_DOWNLOAD = 'svg/no-download-file.svg';
    public const SVG_INFO = 'svg/information-circle.svg';
    public const SVG_PERSONAL_DATE = 'svg/personal-data.svg';
    public const SVG_CERTIFICATE = 'svg/certificate.svg';
    public const SVG_STATUS = 'svg/status.svg';
    public const SVG_ARCHIVE = 'svg/archive.svg';
    public const SVG_ALERT_WARNING = 'svg/alert-warning.svg';
    public const SVG_ALERT_DANGER = 'svg/alert-danger.svg';
    public const SVG_ALERT_INFO = 'svg/alert-info.svg';
    public const SVG_TURNOUT = 'svg/turnout.svg';   // явка
    public const SVG_NON_APPEARANCE = 'svg/non-appearance.svg';   // неявка
    public const SVG_DISTANT = 'svg/distant.svg';   // дистант
    public const SVG_DROPPED = 'svg/dropped.svg';   // выбыл/отчислен
    public const SVG_CHECK = 'svg/do-circle.svg';
    public const SVG_CROSS = 'svg/dont-circle.svg';
    public const SVG_UP = 'svg/up.svg';
    public const SVG_PROJECT = 'svg/project.svg';
    public const SVG_MAIL = 'svg/mail.svg';
    public const SVG_ACHIEVEMENT = 'svg/achievement-events.svg';
    public const SVG_INSTRUCTION = 'svg/instruction.svg';
    public const SVG_DOCUMENT_FLOW = 'svg/document-flow.svg';
    public const SVG_DIRECTORY = 'svg/directory.svg';
    public const SVG_EDUCATIONAL = 'svg/educational.svg';
    public const SVG_ANALYTIC = 'svg/analytic.svg';


    public const CERTIFICATE_TEMPLATES = self::BASE_FILEPATH . '/certificate-templates/';
    public const REPORT_TEMPLATES = self::TEMPLATE_FILEPATH . '/report-templates/';
}