<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\components;


/**
 * Class MenuRouterUrlRuleParse
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRouterUrlRuleParse extends MenuRouterUrlRule
{
    /**
     * @var string
     */
    public $menuRoute;

    /**
     * @inheritdoc
     */
    public function process($requestInfo, $menuUrlRule)
    {
        if ($this->menuRoute && $this->menuRoute != $requestInfo->menuRoute) {
            return false;
        }

        return $menuUrlRule->getRouter($this->router)->{$this->handler}($requestInfo);
    }
} 