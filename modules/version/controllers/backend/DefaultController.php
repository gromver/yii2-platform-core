<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\version\controllers\backend;


use gromver\widgets\ModalIFrame;
use gromver\platform\core\modules\version\models\Version;
use gromver\platform\core\modules\version\models\VersionSearch;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * Class DefaultController implements the CRUD actions for Version model.
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \gromver\platform\core\controllers\BackendController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'delete'],
                    'bulk-delete' => ['post'],
                    'restore' => ['post'],
                    'keep-forever' => ['post'],
                    'bulk-keep-forever' => ['post'],
                ]
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'preview', 'compare', 'item'],
                        'roles' => ['readVersion'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'restore', 'keep-forever', 'bulk-keep-forever'],
                        'roles' => ['updateVersion'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'bulk-delete'],
                        'roles' => ['deleteVersion'],
                    ],

                ]
            ]
        ];
    }

    /**
     * Lists all Version models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VersionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Version model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Version model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    /*public function actionCreate()
    {
        $model = new Version();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }*/

    /**
     * Updates an existing Version model.
     * @param integer $id
     * @param null $modal
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $modal = null)
    {
        $model = $this->findModel($id);
        if ($modal) {
            Yii::$app->applyModalLayout();
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $modal ? $this->redirect(['item', 'item_id' => $model->item_id, 'item_class' => $model->item_class]) : $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Version model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        if(Yii::$app->request->getIsDelete())
            return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));

        return $this->redirect(['index']);
    }

    /**
     * @return \yii\web\Response
     * @throws \Exception
     */
    public function actionBulkDelete()
    {
        $ids = Yii::$app->request->getBodyParam('id', []);

        $models = Version::findAll(['id' => $ids]);

        foreach($models as $model) {
            $model->delete();
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * @param integer $id
     * @param null $modal
     * @throws HttpException
     * @throws NotFoundHttpException
     */
    public function actionRestore($id, $modal = null)
    {
        $model = $this->findModel($id);
        if ($model->restore()) {
            if ($modal) {
                ModalIFrame::refreshParent();
            } else {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        throw new HttpException(409, Yii::t('gromver.platform', "Version restore is failed:\n{errors}", ['errors' => implode("\n", $model->getRestoreErrors())]));
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPreview($id)
    {
        Yii::$app->applyModalLayout();

        return $this->render('preview', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @param integer $id1
     * @param integer $id2
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCompare($id1, $id2)
    {
        Yii::$app->applyModalLayout();

        return $this->render('compare', [
            'a' => $this->findModel($id1),
            'b' => $this->findModel($id2),
        ]);
    }

    /**
     * @param integer $item_id
     * @param string $item_class
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionItem($item_id, $item_class)
    {
        $item = $this->findItemModel($item_id, $item_class);

        $dataProvider = new ActiveDataProvider([
            'query' => $item->getVersions(),
            'pagination' => false
        ]);

        Yii::$app->applyModalLayout();

        return $this->render('item', [
            'item' => $item,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionKeepForever($id)
    {
        $model = $this->findModel($id);

        $model->keep_forever = $model->keep_forever ? 0 : 1;
        $model->save(false);

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['view', 'id' => $model->id]));
    }

    /**
     * @return \yii\web\Response
     */
    public function actionBulkKeepForever()
    {
        $models = Version::find()->andWhere(['id' => Yii::$app->request->post('id', [])])->all();

        foreach($models as $model) {
            $model->keep_forever = $model->keep_forever ? 0 : 1;
            $model->save(false);
        }

        return $this->redirect(ArrayHelper::getValue(Yii::$app->request, 'referrer', ['index']));
    }

    /**
     * Finds the Version model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Version the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Version::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param integer $id
     * @param string $class
     * @throws NotFoundHttpException
     * @return Version's item model
     */
    protected function findItemModel($id, $class)
    {
        if ($id !== null && ($model = $class::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
