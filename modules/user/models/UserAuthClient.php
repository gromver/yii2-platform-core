<?php

namespace gromver\platform\core\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%core_user_auth_client}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $source         client id
 * @property string $source_id      client user id
 *
 * @property User $user
 */
class UserAuthClient extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%core_user_auth_client}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'source', 'source_id'], 'required'],
            [['user_id'], 'integer'],
            [['source', 'source_id'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'user_id' => Yii::t('gromver.platform', 'User ID'),
            'source' => Yii::t('gromver.platform', 'Source'),
            'source_id' => Yii::t('gromver.platform', 'Source ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
