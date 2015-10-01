<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\auth\models;


use Yii;

/**
 * Class SignupForm
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class SignupForm extends \yii\base\Model
{
    const SCENARIO_WITH_CAPTCHA = 'withCaptcha';

    public $username;
    public $email;
    public $password;
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password', 'email'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => '20'],
//            [['username'], 'unique', 'targetClass' => Yii::$app->user->identityClass,  'message' => Yii::t('gromver.platform', 'This username has already been taken.')],
            [['username'], 'unique', 'targetClass' => 'gromver\platform\core\modules\user\models\User',  'message' => Yii::t('gromver.platform', 'This username has already been taken.')],
            [['username'], 'string', 'max' => 128],
            [['email'], 'email'],
//            [['email'], 'unique', 'targetClass' => Yii::$app->user->identityClass, 'message' => Yii::t('gromver.platform', 'This email address has already been taken.')],
            [['email'], 'unique', 'targetClass' => 'gromver\platform\core\modules\user\models\User', 'message' => Yii::t('gromver.platform', 'This email address has already been taken.')],
            // password is validated by validatePassword()
            //['password', 'validatePassword'],
            [['password'], 'string', 'min' => 6],
            [['verifyCode'], 'captcha', 'captchaAction' => 'auth/default/captcha', 'on' => $this::SCENARIO_WITH_CAPTCHA]
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validatePassword()
    {
//        if (!$this->hasErrors()) {
//            $user = $this->getUser();
//            if (!$user || !$user->validatePassword($this->password)) {
//                $this->addError('password', Yii::t('gromver.platform', 'Incorrect username or password.'));
//            }
//        }
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            $this::SCENARIO_WITH_CAPTCHA => ['username', 'email', 'password', 'verifyCode'],
        ] + parent::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('gromver.platform', 'Username'),
            'email' => Yii::t('gromver.platform', 'Email'),
            'password' => Yii::t('gromver.platform', 'Password'),
            'verifyCode' => Yii::t('gromver.platform', 'Verification Code'),
        ];
    }
}