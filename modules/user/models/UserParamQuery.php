<?php

namespace gromver\platform\core\modules\user\models;

/**
 * This is the ActiveQuery class for [[UserParam]].
 *
 * @see UserParams
 */
class UserParamQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return UserParam[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return UserParam|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}