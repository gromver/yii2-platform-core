<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\main\models;


use gromver\platform\core\components\ParamsObject;
use Yii;

/**
 * Class MainParams
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MainParams extends ParamsObject
{
    /**
     * @translation gromver.platform
     */
    public $siteName;
    /**
     * @translation gromver.platform
     */
    public $siteTitle;
    /**
     * @translation gromver.platform
     */
    public $siteSlogan;
    /**
     * @field multiple
     * @multyField text
     * @email
     * @translation gromver.platform
     */
    public $adminEmail;
    /**
     * @field object
     * @object gromver\platform\core\modules\main\models\SupportSubParams
     * @translation gromver.platform
     */
    public $supportEmail;
    /**
     * @before <h3 class="col-sm-offset-3 col-sm-9">Metadata</h3>
     * @translation gromver.platform
     * @label Meta description
     */
    public $description;
    /**
     * @translation gromver.platform
     * @label Meta keywords
     */
    public $keywords;
    /**
     * @field media
     * @translation gromver.platform
     * @label Meta image
     */
    public $image;
    /**
     * @field list
     * @items robots
     * @translation gromver.platform
     * @label Robots
     */
    public $robots;

    public static function robots()
    {
        return [
            '' => Yii::t('gromver.platform', 'Empty'),
            'index, follow' => 'Index, Follow',
            'noindex, follow' => 'No index, follow',
            'index, nofollow' => 'Index, No follow',
            'noindex, nofollow' => 'No index, no follow'
        ];
    }

    /**
     * @return string
     */
    public static function paramsName()
    {
        return Yii::t('gromver.platform', 'Main Params');
    }

    /**
     * @return string
     */
    public static function paramsType()
    {
        return 'main';
    }
}

class SupportSubParams
{
    /**
     * @translation gromver.platform
     * @email
     * @label Email
     */
    public $fromEmail = 'support@example.com';
    /**
     * @translation gromver.platform
     * @label Name
     */
    public $fromName = 'My Site Support';
}