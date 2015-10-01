<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\auth\models;


use gromver\platform\core\modules\user\models\User;
use Yii;

/**
 * Class LoginForm
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class LoginForm extends \yii\base\Model
{
    const SCENARIO_WITH_CAPTCHA = 'withCaptcha';

    public $username;
    public $password;
    public $rememberMe = true;
    public $verifyCode;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            [['rememberMe'], 'boolean'],
            // password is validated by validatePassword()
            [['password'], 'validatePassword'],
            [['verifyCode'], 'captcha', 'captchaAction' => 'auth/default/captcha', 'on' => $this::SCENARIO_WITH_CAPTCHA]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            $this::SCENARIO_WITH_CAPTCHA => ['username', 'email', 'password', 'rememberMe', 'verifyCode'],
        ] + parent::scenarios();
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', Yii::t('gromver.platform', 'Incorrect username or password.'));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('gromver.platform', 'Username or Email'),
            'password' => Yii::t('gromver.platform', 'Password'),
            'rememberMe' => Yii::t('gromver.platform', 'Remember Me'),
            'verifyCode' => Yii::t('gromver.platform', 'Verification Code'),
        ];
    }
    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? Yii::$app->getModule('auth')->rememberMeTime : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::find()->published()->andWhere(['or', ['username' => $this->username], ['email' => $this->username]])->one();
        }

        return $this->_user;
    }
}