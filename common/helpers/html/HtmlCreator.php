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

    public static function IconDelete() {
        return '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>';
    }

    public static function archiveTooltip() {
        return HtmlBuilder::createTooltipIcon(
            'Архивный объект защищён от изменений',
            FilePaths::SVG_ARCHIVE
        );
    }
}