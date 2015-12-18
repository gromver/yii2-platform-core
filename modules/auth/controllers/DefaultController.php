<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\auth\controllers;


use gromver\platform\core\modules\auth\models\ForgotPasswordForm;
use gromver\platform\core\modules\auth\models\LoginForm;
use gromver\platform\core\modules\auth\models\SignupForm;
use gromver\platform\core\modules\user\models\User;
use gromver\widgets\ModalIFrame;
use kartik\widgets\Alert;
use Yii;
use yii\di\Instance;
use yii\filters\AccessControl;
use yii\mail\BaseMailer;
use yii\web\BadRequestHttpException;

/**
 * Class DefaultController
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\core\modules\auth\Module $module
 */
class DefaultController extends \yii\web\Controller
{
    public $mailer = 'mailer';

    private $loginAttemptsVar = '__loginAttemptsCount';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'captcha' => $this->module->captchaConfig
        ];
    }

    public function actionLogin($modal = null)
    {
        /*if (!\Yii::$app->user->isGuest) {
            $this->goHome();
        }*/

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post())) {
            if($model->login()) {
                $this->setLoginAttempts(0); //if login is successful, reset the attempts

                if ($modal) {
                    ModalIFrame::refreshParent();
                }

                return $this->goBack();
            } else {
                //if login is not successful, increase the attempts
                $this->setLoginAttempts($this->getLoginAttempts() + 1);
                Yii::$app->session->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', 'Authorization is failed.'));
            }
        }

        //make the captcha required if the unsuccessful attempts are more of thee
        if ($this->getLoginAttempts() >= $this->module->attemptsBeforeCaptcha) {
            $model->scenario = LoginForm::SCENARIO_WITH_CAPTCHA;
        }

        if ($modal) {
            Yii::$app->applyModalLayout();
        } elseif ($this->module->authLayout) {
            Yii::$app->layout = $this->module->authLayout;
        }


        return $this->render('login', [
            'model' => $model,
        ]);
    }

    private function getLoginAttempts()
    {
        return Yii::$app->getSession()->get($this->loginAttemptsVar, 0);
    }

    private function setLoginAttempts($value)
    {
        Yii::$app->getSession()->set($this->loginAttemptsVar, $value);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup($modal = null)
    {
        $model = new SignupForm();
        //$model->scenario = $model::SCENARIO_WITH_CAPTCHA;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = new User();
            $user->username = $model->username;
            $user->email = $model->email;
            $user->password = $model->password;

            if ($user->save() && Yii::$app->getUser()->login($user)) {
                Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Registration complete.'));

                if ($modal) {
                    ModalIFrame::refreshParent();
                }

                return $this->goBack();
            }
        }

        if ($modal) {
            Yii::$app->applyModalLayout();
        } elseif ($this->module->authLayout) {
            Yii::$app->layout = $this->module->authLayout;
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordResetToken($modal = null)
    {
        $model = new ForgotPasswordForm();
        $model->scenario = ForgotPasswordForm::SCENARIO_REQUEST;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($this->sendPasswordResetEmail($model->email)) {
                Yii::$app->getSession()->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Check your email for further instructions.'));

                if ($modal) {
                    ModalIFrame::refreshParent();
                }

                return $this->goBack();
            } else {
                Yii::$app->getSession()->setFlash(Alert::TYPE_DANGER, Yii::t('gromver.platform', 'There was an error sending email.'));
            }
        }

        if ($modal) {
            Yii::$app->applyModalLayout();
        } elseif ($this->module->authLayout) {
            Yii::$app->layout = $this->module->authLayout;
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        /** @var User $user */
        $user = User::findOne([
            'password_reset_token' => $token,
            'status' => User::STATUS_ACTIVE,
        ]);

        if (!$user) {
            throw new BadRequestHttpException(Yii::t('gromver.platform', 'Wrong password reset token.'));
        }

        $model = new ForgotPasswordForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user->password = $model->password;

            if ($user->save()) {
                Yii::$app->getSession()->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'New password has been saved.'));

                return $this->goHome();
            }
        }

        if ($this->module->authLayout) {
            Yii::$app->layout = $this->module->authLayout;
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    private function sendPasswordResetEmail($email)
    {
        /** @var User $user */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $email,
        ]);

        if (!$user) {
            return false;
        }

        //$user->password_reset_token = Yii::$app->security->generateRandomString();
        $user->generatePasswordResetToken();
        if ($user->save(false)) {
            /** @var \gromver\platform\core\modules\auth\Module $authModule */
            $authModule = $this->module;
            $mailer = Instance::ensure($authModule->mailer, BaseMailer::className());

            return $mailer->compose($authModule->emailLayoutPasswordResetToken, ['user' => $user])
                ->setFrom(Yii::$app->supportEmail)
                ->setTo($user->email)
                ->setSubject(Yii::t('gromver.platform', 'Password reset for {name}.', ['name' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']]))
                ->send();
        }

        return false;
    }
}
