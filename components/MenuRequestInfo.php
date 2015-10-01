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
 * Class MenuRequestInfo
 * Обертка для данных о запросе
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuRequestInfo extends \yii\base\Object
{
    /**
     * Карта меню в контексте которой рассматривается текущий запрос
     * @var MenuMap
     */
    public $menuMap;
    /**
     * Роут на который ссылается активный пункт меню, см. \gromver\platform\core\components\MenuUrlRule::parseRequest
     * @var string
     */
    public $menuRoute;
    /**
     * Параметры меню, извлекаются из ссылки на которую ссылается пункт меню, см. \gromver\platform\core\components\MenuUrlRule::parseRequest и \gromver\platform\core\modules\menu\models\MenuItem::parseUrl
     * @var array
     */
    public $menuParams;
    /**
     * Роут запроса (в контексте \gromver\platform\core\components\MenuUrlRule::createUrl) либо
     * необработаный роут запроса (в контексте \gromver\platform\core\components\MenuUrlRule::parseRequest)
     * необработанный роут = роут запроса - путь "подходящего" пункта меню
     * @var string
     */
    public $requestRoute;
    /**
     * Параметры запроса
     * @var array
     */
    public $requestParams;
}