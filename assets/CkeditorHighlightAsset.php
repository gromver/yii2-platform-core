<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\assets;


/**
 * Class CkeditorHighlightAsset
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class CkeditorHighlightAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@gromver/platform/core/assets/ckeditor';
    public $js = ['plugins/codesnippet/lib/highlight/highlight.pack.js'];
    public $css = ['plugins/codesnippet/lib/highlight/styles/default.css'];

    public function init() {
        parent::init();
        \Yii::$app->view->registerJs('hljs.initHighlightingOnLoad();');
    }
} 