<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\components;


use gromver\modulequery\ModuleEventsInterface;
use gromver\platform\core\behaviors\SearchBehavior;
use gromver\platform\core\interfaces\model\SearchableInterface;
use gromver\platform\core\interfaces\model\ViewableInterface;
use yii\base\InvalidParamException;

/**
 * Class BaseSearchModule
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class BaseSearchModule extends \yii\base\Module implements ModuleEventsInterface
{
    /**
     * Инедксация сохраненой модели для последующего поиска по этому индексу
     * @param \gromver\platform\core\behaviors\SearchBehaviorEvent $event
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
     * @param \gromver\platform\core\behaviors\SearchBehaviorEvent $event
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
        ];
    }
} 