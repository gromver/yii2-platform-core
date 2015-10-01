<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\search\modules\sql\controllers\backend;


use yii\filters\AccessControl;
use Yii;

/**
 * Class DefaultController
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \gromver\platform\core\controllers\BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['searchSql'],
                    ]
                ]
            ]
        ];
    }

    public function actionIndex($q = null)
    {
        return $this->render('index', [
            'query' => $q
        ]);
    }
}
