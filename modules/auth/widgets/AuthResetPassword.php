<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\auth\widgets;


use gromver\platform\core\modules\user\models\User;
use yii\base\Widget;

/**
 * Class AuthResetPassword
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class AuthResetPassword extends Widget
{
    /**
     * @var string|array
     */
    public $url;
    /**
     * @var User
     */
    public $model;
    /**
     * @var string
     */
    public $layout = 'resetPassword';

    public function init()
    {
        parent::init();

        if (!isset($this->model)) {
            $this->model = new User();
        }
    }

    public function run()
    {
        echo $this->render($this->layout, [
            'model' => $this->model,
            'url' => $this->url,
        ]);
    }
}