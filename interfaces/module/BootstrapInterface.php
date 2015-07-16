<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\interfaces\module;

/**
 * Interface BootstrapInterface
 * Используется модулями для автоматического бутсрапа в Grom Platform
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
interface BootstrapInterface
{
    /**
     * @param $app \yii\base\Application
     */
    public function bootstrap($app);
}