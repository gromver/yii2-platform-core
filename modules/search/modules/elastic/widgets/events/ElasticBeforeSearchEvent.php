<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\modules\elastic\widgets\events;


/**
 * Class ElasticBeforeSearchEvent
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\core\modules\search\modules\elastic\widgets\SearchResultsFrontend $sender
 */
class ElasticBeforeSearchEvent extends \gromver\modulequery\Event {
    /**
     * Класс модели, которая участвует в поиске и для которой поисковый виджет запрашивает модификацию поискового запроса
     * Актуально для фронтенда, где данные могут быть не опубликованы или скрыты для публичного доступа по другим причинам
     * @var string
     */
    public $modelClass;
    /**
     * @var \yii\elasticsearch\Query
     */
    public $query;
    /**
     * @var bool
     */
    public $skip = false;
}