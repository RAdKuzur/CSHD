<?php


namespace common\components\logger;


use common\components\logger\search\SearchLog;

class SearchLogFacade
{
    public static function findLogs(SearchLog $searchProvider)
    {
        $logs = $searchProvider->findByBaseData();
        return $searchProvider->findByAddData($logs);
    }
}