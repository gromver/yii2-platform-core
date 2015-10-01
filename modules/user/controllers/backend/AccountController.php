<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\user\controllers\backend;


use gromver\models\ObjectModel;
use gromver\platform\core\modules\user\models\User;
use kartik\widgets\Alert;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class AccountController
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\core\modules\user\Module $module
 */
class AccountController extends \gromver\platform\core\controllers\BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'reset-password'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['params'],
                        'roles' => ['administrator'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        /** @var User $user */
        $model = Yii::$app->user->getIdentity();

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionResetPassword()
    {
        /** @var User $model */
        $model = Yii::$app->user->getIdentity();
        $model->scenario = User::SCENARIO_RESET_PASSWORD;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', "Your password has been changed."));
            return $this->redirect(['index']);
        } else {
            return $this->render('reset-password', [
                'model' => $model,
            ]);
        }
    }

    /**
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionParams()
    {
        /** @var User $user */
        $user = Yii::$app->user->getIdentity();

        return $this->render('params', [
            'user' => $user,
            'params' => $user->params,
        ]);
    }
}
