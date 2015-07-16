<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\user\models;


use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class UserSearch represents the model behind the search form about `gromver\platform\core\modules\user\models\User`.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at', 'deleted_at', 'last_visit_at'], 'integer'],
            [['username', 'email', 'password_hash', 'password_reset_token', 'auth_key', 'roles'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    /*public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }*/

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @param bool $trashed
     * @return ActiveDataProvider
     */
    public function search($params, $trashed = false)
    {
        $query = User::find();

        if ($trashed) {
            $query->trashed();
        } else {
            $query->published();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['updated_at' => SORT_DESC]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'last_visit_at' => $this->last_visit_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key]);

        if (count($this->getRoles())) {
            /** @var \yii\rbac\DbManager $auth */
            $auth = Yii::$app->authManager;

            $query->leftJoin($auth->assignmentTable . ' roles', 'roles.user_id=id');
            $query->andWhere(['roles.item_name'=>$this->getRoles()]);
        }

        return $dataProvider;
    }
}
