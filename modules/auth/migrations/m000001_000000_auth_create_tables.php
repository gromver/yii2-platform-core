<?php

use yii\db\Schema;

class m000001_000000_auth_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // auth rule
        $this->createTable('{{%core_auth_rule}}', [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ]);

        // auth item
        $this->createTable('{{%core_auth_item}}', [
            'name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'type' => Schema::TYPE_INTEGER . ' NOT NULL',
            'description' => Schema::TYPE_TEXT,
            'rule_name' => Schema::TYPE_STRING . '(64)',
            'data' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER,
            'updated_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (name)',
        ]);

        $this->addForeignKey('Core_AuthItem_RuleName_fk', '{{%core_auth_item}}', 'rule_name', '{{%core_auth_rule}}', 'name', 'SET NULL', 'CASCADE');
        $this->createIndex('Type_idx', '{{%core_auth_item}}', 'type');

        // auth item child
        $this->createTable('{{%core_auth_item_child}}', [
            'parent' => Schema::TYPE_STRING . '(64) NOT NULL',
            'child' => Schema::TYPE_STRING . '(64) NOT NULL',
            'PRIMARY KEY (parent,child)',
        ]);
        $this->addForeignKey('Core_AuthItemChild_Parent_fk', '{{%core_auth_item_child}}', 'parent', '{{%core_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('Core_AuthItemChild_Child_fk', '{{%core_auth_item_child}}', 'child', '{{%core_auth_item}}', 'name', 'CASCADE', 'CASCADE');
        // auth assignment
        $this->createTable('{{%core_auth_assignment}}', [
            'item_name' => Schema::TYPE_STRING . '(64) NOT NULL',
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER,
            'PRIMARY KEY (item_name,user_id)',
        ]);
        $this->addForeignKey('Core_AuthAssignment_ItemName_fk', '{{%core_auth_assignment}}', 'item_name', '{{%core_auth_item}}', 'name', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%core_auth_assignment}}');
        $this->dropTable('{{%core_auth_item_child}}');
        $this->dropTable('{{%core_auth_item}}');
        $this->dropTable('{{%core_auth_rule}}');
    }
}