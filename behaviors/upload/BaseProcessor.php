<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\behaviors\upload;


/**
 * Class BaseProcessor
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
abstract class BaseProcessor extends \yii\base\Object
{
    public function process($filePath) {}
}