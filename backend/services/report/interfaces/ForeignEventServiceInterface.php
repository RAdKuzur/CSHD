<?php


namespace backend\services\report\interfaces;


use backend\services\report\ReportFacade;

interface ForeignEventServiceInterface
{
    public function calculateEventParticipants(
        string $startDate,
        string $endDate,
        array $branches,
        array $focuses,
        array $allowRemotes,
        array $levels = [],
        int $mode = ReportFacade::MODE_PURE
    );
}