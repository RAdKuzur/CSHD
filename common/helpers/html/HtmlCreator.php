<?php

namespace common\helpers\html;

use common\helpers\files\FilePaths;

class HtmlCreator
{
    public static function filterToggle() {
        return '<div class="filter-toggle" id="filterToggle">
                    <svg width="42" height="42" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <title>filters</title>
                        <path d="M9 12L4 4H15M20 4L15 12V21L9 18V16" stroke="#000000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>';
    }

    public static function filterHeaderForm() {
        return '<h3>
                    <svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path d="M9 12L4 4H15M20 4L15 12V21L9 18V16" stroke="#009580" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg> Фильтры поиска:
                </h3>';
    }

    public static function archiveTooltip() {
        return HtmlBuilder::createTooltipIcon(
            'Архивный объект защищён от изменений',
            FilePaths::SVG_ARCHIVE
        );
    }
}