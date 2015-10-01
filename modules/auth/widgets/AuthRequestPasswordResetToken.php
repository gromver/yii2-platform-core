<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\auth\widgets;


use gromver\platform\core\modules\auth\models\ForgotPasswordForm;
use gromver\platform\core\modules\user\models\User;
use yii\base\Widget;

/**
 * Class AuthRequestPasswordResetToken
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class AuthRequestPasswordResetToken extends Widget
{
    /**
     * @var string|array
     */
    public $url;
    /**
     * @var string|array
     */
    public $withCaptcha = false;
    /**
     * @var string|array
     */
    public $captchaAction = '/auth/default/captcha';
    /**
     * @var User
     */
    public $model;
    /**
     * @var string
     */
    public $layout = 'requestPasswordResetToken';

    public function init()
    {
        parent::init();

        if (!isset($this->model)) {
            $this->model = new ForgotPasswordForm();
        }

        if ($this->withCaptcha) {
            $this->model->scenario = ForgotPasswordForm::SCENARIO_REQUEST_WITH_CAPTCHA;
        } else {
            $this->model->scenario = ForgotPasswordForm::SCENARIO_REQUEST;
        }
    }

    public function run()
    {
        echo $this->render($this->layout, [
            'model' => $this->model,
            'url' => $this->url,
            'captchaAction' => $this->captchaAction,
        ]);
    }
}