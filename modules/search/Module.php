<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\modules\search\behaviors\SearchBehavior;
use gromver\platform\core\interfaces\model\SearchableInterface;
use gromver\platform\core\interfaces\model\ViewableInterface;
use gromver\platform\core\modules\search\behaviors\events\SearchBehaviorEvent;
use gromver\platform\core\modules\search\widgets\SearchResultsBackend;
use gromver\platform\core\modules\search\widgets\SearchResultsFrontend;
use yii\base\InvalidParamException;
use Yii;

/**
 * Class Module
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Module extends \yii\base\Module implements ModuleEventsInterface
{
    /**
     * Список моделей открытых для поиска во фронтенде
     * [
     *      'foo\bar\news\Model' => 'Новости',
     *      'foo\bar\page\Model' => 'Страницы',
     * ]
     * @var array
     */
    public $frontendSearchableModels = [];
    /**
     * Список моделей открытых для поиска в бэкенде
     * [
     *      'foo\bar\news\Model' => 'Новости',
     *      'foo\bar\page\Model' => 'Страницы',
     *      'foo\bar\user\Model' => 'Пользователи',
     * ]
     * @var array
     */
    public $backendSearchableModels = [];

    /**
     * Инедксация сохраненой модели для последующего поиска по этому индексу
     * @param SearchBehaviorEvent $event
     * @return bool|null
     * @throw InvalidParamException
     */
    public function indexPage($event)
    {
        if (!$event->model instanceof SearchableInterface) {
            throw new InvalidParamException(__CLASS__ . '::indexPage($event). $event->model must be an \gromver\platform\core\interfaces\model\SearchableInterface instance.');
        }

        if (!$event->model instanceof ViewableInterface) {
            throw new InvalidParamException(__CLASS__ . '::indexPage($event). $event->model must be an \gromver\platform\core\interfaces\model\ViewableInterface instance.');
        }
    }

    /**
     * Удаление записи из индекса соответсвующей удаленной модели
     * @param SearchBehaviorEvent $event
     * @return bool|null
     * @throw InvalidParamException
     */
    public function deletePage($event) {}

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            SearchBehavior::EVENT_INDEX_PAGE => [$this, 'indexPage'],
            SearchBehavior::EVENT_DELETE_PAGE => [$this, 'deletePage'],
            SearchResultsBackend::EVENT_FETCH_SEARCHABLE_MODELS => 'addSearchableModelsBackend',
            SearchResultsFrontend::EVENT_FETCH_SEARCHABLE_MODELS => 'addSearchableModelsFrontend'
        ];
    }

    /**
     * @param $event \gromver\platform\core\modules\search\widgets\events\SearchableModelsEvent
     */
    public function addSearchableModelsBackend($event)
    {
        $event->items = array_merge($event->items, $this->backendSearchableModels);
    }

    /**
     * @param $event \gromver\platform\core\modules\search\widgets\events\SearchableModelsEvent
     */
    public function addSearchableModelsFrontend($event)
    {
        $event->items = array_merge($event->items, $this->frontendSearchableModels);
    }
} 