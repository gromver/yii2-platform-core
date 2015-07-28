<?php

use yii\db\Schema;

class m000008_000000_page_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // page
        $this->createTable('{{%core_page}}', [
            'id' => Schema::TYPE_PK,
            'parent_id' => Schema::TYPE_INTEGER . ' DEFAULT NULL',
            'title' => Schema::TYPE_STRING . '(1024)',
            'alias' => Schema::TYPE_STRING,
            'path' => Schema::TYPE_STRING . '(2048)',
            'preview_text' => Schema::TYPE_TEXT,
            'detail_text' => Schema::TYPE_TEXT,
            'metakey' => Schema::TYPE_STRING,
            'metadesc' => Schema::TYPE_STRING . '(2048)',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'status' => Schema::TYPE_SMALLINT,
            'created_by' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_by' => Schema::TYPE_INTEGER,
            'lft' => Schema::TYPE_INTEGER,
            'rgt' => Schema::TYPE_INTEGER,
            'level' => Schema::TYPE_SMALLINT . ' UNSIGNED',
            'ordering' => Schema::TYPE_INTEGER . ' UNSIGNED',
            'hits' => Schema::TYPE_BIGINT . ' UNSIGNED',
            'lock' => Schema::TYPE_BIGINT . ' UNSIGNED DEFAULT 1',
        ]);
        $this->createIndex('Alias_idx', '{{%core_page}}', 'alias');
        $this->createIndex('Path_idx', '{{%core_page}}', 'path');
        $this->createIndex('Status_idx', '{{%core_page}}', 'status');
        $this->createIndex('ParentId_idx', '{{%core_page}}', 'parent_id');
        $this->createIndex('Lft_Rgt_idx', '{{%core_page}}', 'lft, rgt');
        //вставляем рутовый элемент
        $this->insert('{{%core_page}}', [
            'status' => 1,
            'title' => 'Root',
            'created_at' => time(),
            'created_by' => 1,
            'lft' => 1,
            'rgt' => 2,
            'level' => 1,
            'ordering' => 1
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%core_page}}');
    }
}