<?php

use yii\db\Schema;

class m000006_000000_version_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // version
        $this->createTable('{{%core_version}}', [
            'id' => Schema::TYPE_PK,
            'item_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'item_class' => Schema::TYPE_STRING . '(1024)',
            'version_note' => Schema::TYPE_STRING,
            'version_hash' => Schema::TYPE_STRING . '(50)',
            'version_data' => Schema::TYPE_TEXT,
            'character_count' => Schema::TYPE_INTEGER . ' UNSIGNED',
            'keep_forever' => Schema::TYPE_BOOLEAN . ' DEFAULT 0',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
        $this->createIndex('Item_idx', '{{%core_version}}', 'item_id');
        $this->createIndex('ItemClass_idx', '{{%core_version}}', 'item_class');
        $this->createIndex('VersionHash_idx', '{{%core_version}}', 'version_hash');
    }

    public function down()
    {
        $this->dropTable('{{%core_version}}');
    }
}