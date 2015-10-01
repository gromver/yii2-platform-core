<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\auth\models;


use Yii;

/**
 * Class ForgotPasswordForm
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ForgotPasswordForm extends \yii\base\Model
{
    const SCENARIO_REQUEST = 'request';
    const SCENARIO_REQUEST_WITH_CAPTCHA = 'requestWithCaptcha';

    public $username;
    public $email;
    public $password;
    public $passwordConfirm;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email'], 'required', 'on' => $this::SCENARIO_REQUEST],
            [['email'], 'email', 'on' => $this::SCENARIO_REQUEST],
            [['password'], 'string', 'min' => 6],
            [['passwordConfirm'], 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],
            [['verifyCode'], 'captcha', 'captchaAction' => 'auth/default/captcha', 'on' => $this::SCENARIO_REQUEST_WITH_CAPTCHA]
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            $this::SCENARIO_REQUEST => ['email'],
            $this::SCENARIO_REQUEST_WITH_CAPTCHA => ['email', 'verifyCode'],
        ] + parent::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('gromver.platform', 'Email'),
            'password' => Yii::t('gromver.platform', 'Password'),
            'passwordConfirm' => Yii::t('gromver.platform', 'Confirm Password'),
            'verifyCode' => Yii::t('gromver.platform', 'Verification Code'),
        ];
    }

//    private $_user = false;
//
//    /**
//     * Finds user by [[username]]
//     *
//     * @return User|null
//     */
//    public function getUser()
//    {
//        if ($this->_user === false) {
//            $this->_user = User::find()($this->email);
//        }
//
//        return $this->_user;
//    }
}