<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\auth\widgets;


use gromver\platform\core\modules\user\models\User;
use yii\base\Widget;

/**
 * Class AuthSignup
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class AuthSignup extends Widget
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
    public $layout = 'signup';

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