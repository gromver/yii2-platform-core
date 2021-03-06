<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\widgets;


use Yii;

/**
 * Class SearchFormFrontend
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SearchFormBackend extends SearchFormFrontend
{
    const EVENT_FETCH_ENDPOINTS = 'SearchEndpointsBackend';

    /**
     * @var string
     * @field list
     * @items url
     * @empty
     */
    public $url = '/search/sql/backend/default/index';
}