<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\tag\models;


/**
 * Class TagToItem
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $tag_id
 * @property integer $item_id
 * @property string $item_class
 * @property \yii\db\ActiveRecord $item
 */
class TagToItem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%core_tag_to_item}}';
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['tag_id', 'item_id', 'item_class'];
    }

    /**
     * @return null|static
     */
    public function getItem()
    {
        /** @var \yii\db\ActiveRecord $itemClass */
        $itemClass = $this->item_class;
        return $itemClass::findOne($this->item_id);
    }
} 