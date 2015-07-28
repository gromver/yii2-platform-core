<?php

use yii\db\Schema;

class m000007_000000_tag_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // tag
        $this->createTable('{{%core_tag}}', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . '(100)',
            'alias' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_SMALLINT,
            'group' => Schema::TYPE_STRING,
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'hits' => Schema::TYPE_BIGINT . ' UNSIGNED',
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('Alias_idx', '{{%core_tag}}', 'alias');
        $this->createIndex('Status_idx', '{{%core_tag}}', 'status');

        // tag_to_item
        $this->createTable('{{%core_tag_to_item}}', [
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'item_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'item_class' => Schema::TYPE_STRING . '(1024) NOT NULL',
        ]);
        $this->createIndex('TagId_ItemId_idx', '{{%core_tag_to_item}}', 'tag_id, item_id');
        $this->addForeignKey('Grom_TagToItem_TagId_fk', '{{%core_tag_to_item}}', 'tag_id', '{{%core_tag}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%core_tag_to_item}}');
        $this->dropTable('{{%core_tag}}');
    }
}