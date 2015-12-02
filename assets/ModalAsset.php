<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\assets;


use yii\web\AssetBundle;

/**
 * Class ModalAsset
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ModalAsset extends AssetBundle {
    public $sourcePath = '@gromver/platform/core/assets/layout';
    public $css = ['css/modal.css'];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

} 