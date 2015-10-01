<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\tag\components;


use gromver\platform\core\components\UrlManager;
use gromver\platform\core\modules\menu\models\MenuItem;
use gromver\platform\core\modules\tag\models\Tag;

/**
 * Class MenuRouterTag
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterTag extends \gromver\platform\core\components\MenuRouter
{
    /**
     * @inheritdoc
     */
    public function parseUrlRules()
    {
        return [
            [
                'menuRoute' => 'tag/frontend/default/index',
                'handler' => 'parseTagCloud'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function createUrlRules()
    {
        return [
            [
                'requestRoute' => 'tag/frontend/default/view',
                'requestParams' => ['id'],
                'handler' => 'createTagItems'
            ],
        ];
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return array
     */
    public function parseTagCloud($requestInfo)
    {
        if (preg_match('/^\d+$/', $requestInfo->requestRoute)) {
            return ['tag/frontend/default/view', ['id' => $requestInfo->requestRoute]];
        } else {
            /** @var Tag $tag */
            if ($tag = Tag::findOne(['alias' => $requestInfo->requestRoute, 'language' => $requestInfo->menuMap->language])) {
                return ['tag/frontend/default/view', ['id' => $tag->id, 'alias' => $tag->alias, UrlManager::LANGUAGE_PARAM => $requestInfo->menuMap->language]];
            }
        }
    }

    /**
     * @param \gromver\platform\core\components\MenuRequestInfo $requestInfo
     * @return mixed|null|string
     */
    public function createTagItems($requestInfo)
    {
        if($path = $requestInfo->menuMap->getMenuPathByRoute('tag/frontend/default/index')) {
            $path .= '/' . (isset($requestInfo->requestParams['alias']) ? $requestInfo->requestParams['alias'] : $requestInfo->requestParams['id']);
            unset($requestInfo->requestParams['id'], $requestInfo->requestParams['alias']);
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }
}