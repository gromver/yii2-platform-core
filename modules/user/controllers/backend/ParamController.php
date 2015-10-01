<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\user\controllers\backend;


use gromver\platform\core\modules\user\models\User;
use gromver\platform\core\modules\user\models\UserParam;
use gromver\platform\core\modules\user\models\UserParamSearch;
use kartik\widgets\Alert;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * Class ParamController implements the CRUD actions for User model.
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \gromver\platform\core\modules\user\Module $module
 */
class ParamController extends \gromver\platform\core\controllers\BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'delete'],
                    'bulk-delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'create', 'update', 'view'],
                        'roles' => ['updateUser'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'bulk-delete'],
                        'roles' => ['deleteUser'],
                    ],
                ]
            ]
        ];
    }

    /**
     * @param $user_id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionIndex($user_id)
    {
        $user = $this->findModel($user_id);

        $searchModel = new UserParamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $user->id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user,
        ]);
    }

    /**
     * Displays a single UserParam model.
     * @param integer $user_id
     * @param string $name
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionView($user_id, $name)
    {
        $user = $this->findModel($user_id);

        if (!$model = $user->params[$name]){
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }

        return $this->render('view', [
            'model' => $model,
            'user' => $user,
        ]);
    }

    /**
     * Creates a new UserParam model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param $user_id
     * @return string|\yii\web\Response
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionCreate($user_id)
    {
        $user = $this->findModel($user_id);

        $model = new UserParam();
        $model->user_id = $user->id;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'user_id' => $model->user_id, 'name' => $model->name]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'user' => $user,
            ]);
        }
    }

    /**
     * Updates an existing UserParam model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $user_id
     * @param string $name
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($user_id, $name)
    {
        $user = $this->findModel($user_id);

        if (!$model = $user->params[$name]){
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }

        $model->scenario = User::SCENARIO_UPDATE;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'user_id' => $model->user_id, 'name' => $model->name]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'user' => $user,
            ]);
        }
    }

    /**
     * Deletes an existing UserParam model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $user_id
     * @param string $name
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($user_id, $name)
    {
        $user = $this->findModel($user_id);

        if (!$model = $user->params[$name]){
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }

        $model->delete();

        if(Yii::$app->request->getIsDelete()) {
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index', 'user_id' => $user->id]));
        }

        return $this->redirect(['index', 'user_id' => $user->id]);
    }

    /**
     * @param integer $user_id
     * @return \yii\web\Response
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionBulkDelete($user_id)
    {
        $user = $this->findModel($user_id);

        $data = Yii::$app->request->getBodyParam('data', []);

        foreach ($data as $param) {
            $user->params[$param['name']] ? $user->params[$param['name']]->delete() : null;
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index', 'user_id' => $user->id]));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var $model User */
        if (($model = User::findOne($id)) !== null) {
            // проверка на суперадминство
            if ($model->getIsSuperAdmin() && $model->id != Yii::$app->user->id) {
                throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            }

            // проверка на право админить данного пользователя
            if (!Yii::$app->user->can('administrateUser', ['user' => $model])) {
                throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
            }

            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested page does not exist.'));
        }
    }
}
