<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\page\widgets\assets;


/**
 * Class PageAsset
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PageAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@gromver/platform/core/modules/page/widgets/assets';
    public $css = [
        'page/css/style.css'
    ];
} 