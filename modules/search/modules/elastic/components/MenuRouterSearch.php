<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\modules\elastic\components;


use gromver\platform\core\modules\menu\models\MenuItem;
use gromver\platform\core\components\MenuRequestInfo;

/**
 * Class MenuRouterSearch
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterSearch extends \gromver\platform\core\components\MenuRouter
{
    /**
     * @inheritdoc
     */
    public function createUrlRules()
    {
        return [
            [
                'requestRoute' => 'search/elastic/frontend/default/index',
                'handler' => 'createSearch'
            ],
        ];
    }

    /**
     * @param $requestInfo MenuRequestInfo;
     * @return mixed|null|string
     */
    public function createSearch($requestInfo)
    {
        if($path = $requestInfo->menuMap->getMenuPathByRoute('search/elastic/frontend/default/index')) {
            return MenuItem::toRoute($path, $requestInfo->requestParams);
        }
    }
}