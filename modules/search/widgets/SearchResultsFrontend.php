<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\widgets;


use gromver\modulequery\ModuleEvent;
use gromver\platform\core\modules\search\widgets\events\SearchableModelsEvent;
use gromver\platform\core\modules\widget\widgets\Widget;
use Yii;

/**
 * Class SearchResultsFrontend
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SearchResultsFrontend extends Widget
{
    const EVENT_FETCH_SEARCHABLE_MODELS = 'SearchableModelsFrontend';

    /**
     * Модели по которым будет производится поиск
     * @var string[]
     * @field list
     * @items models
     * @empty All
     * @translation gromver.platform
     * @multiple
     */
    public $models;
    public $query;

    /**
     * @return array
     */
    public static function models()
    {
        return ModuleEvent::trigger(static::EVENT_FETCH_SEARCHABLE_MODELS, new SearchableModelsEvent([
            'items' => [],
        ]), 'items');
    }
}