<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\menu\models;


use Yii;

/**
 * Class MenuLinkParams
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class MenuLinkParams extends \yii\base\Model
{
    public $title;
    public $class;
    public $style;
    public $target;
    public $onclick;
    public $rel;

    public function rules()
    {
        return [
            [['title', 'class', 'style', 'target', 'onclick', 'rel'], 'string']
        ];
    }
}