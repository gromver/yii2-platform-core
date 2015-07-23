<?php

use yii\db\Schema;

class m000003_000000_user_create_tables extends \yii\db\Migration
{
    public function up()
    {
        // user
        $this->createTable('{{%grom_user}}', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . '(64) NOT NULL',
            'email' => Schema::TYPE_STRING . '(128) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . '(128) NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING . '(32)',
            'auth_key' => Schema::TYPE_STRING . '(128)',
            'profile_data' => Schema::TYPE_TEXT,
            'status' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT ' . \gromver\platform\core\modules\user\models\User::STATUS_ACTIVE,
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER,
            'deleted_at' => Schema::TYPE_INTEGER,
            'last_visit_at' => Schema::TYPE_INTEGER,
        ]);

        $this->createIndex('Username_idx', '{{%grom_user}}', 'username');
        $this->createIndex('Email_idx', '{{%grom_user}}', 'email');
        $this->createIndex('Status_idx', '{{%grom_user}}', 'status');

        // user_param
        $this->createTable('{{%grom_user_param}}', [
            'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'name' => Schema::TYPE_STRING . '(50) NOT NULL',
            'value' => Schema::TYPE_TEXT,
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);
        $this->addPrimaryKey('UserId_Name_pk', '{{%grom_user_param}}', ['user_id', 'name']);
        $this->createIndex('Value_idx', '{{%grom_user_param}}', 'value(50)');
        $this->addForeignKey('Grom_UserParam_User_fk', '{{%grom_user_param}}', 'user_id', '{{%grom_user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%grom_user_param}}');
        $this->dropTable('{{%grom_user}}');
    }
}