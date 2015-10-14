<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\main\widgets\assets;


/**
 * Class PlatformAsset
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PlatformAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@gromver/platform/core/modules/main/widgets/assets/platform';
    public $css = [
        'css/style.css'
    ];
} 