<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\modules\sql\widgets\events;


/**
 * Class SqlBeforeSearchEvent
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $sender \gromver\platform\core\modules\search\modules\sql\widgets\SearchResultsFrontend|\gromver\platform\core\modules\search\modules\sql\widgets\SearchResultsBackend
 */
class SqlBeforeSearchEvent extends \gromver\modulequery\Event {
    /**
     * Класс модели, которая участвует в поиске и для которой поисковый виджет запрашивает модификацию поискового запроса
     * Актуально для фронтенда, где данные могут быть не опубликованы или скрыты для публичного доступа по другим причинам
     * @var string
     */
    public $modelClass;
    /**
     * @var \yii\db\Query
     */
    public $query;
    /**
     * @var bool
     */
    public $skip = false;
}