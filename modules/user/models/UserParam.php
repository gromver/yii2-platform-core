<?php

namespace gromver\platform\core\modules\user\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%grom_user_param}}".
 *
 * @property integer $user_id
 * @property string $name
 * @property string $value
 * @property integer $created_at
 *
 * @property User $user
 */
class UserParam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%grom_user_param}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'name'], 'required'],
            [['user_id', 'created_at'], 'integer'],
            [['value'], 'string'],
            [['name'], 'string', 'max' => 50],
            [['user_id'], 'exist'/*, 'skipOnError' => true*/, 'targetClass' => User::className(), 'targetAttribute' => 'id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('gromver.platform', 'User ID'),
            'name' => Yii::t('gromver.platform', 'Name'),
            'value' => Yii::t('gromver.platform', 'Value'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return UserParamQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserParamQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false
            ]
        ];
    }
}
