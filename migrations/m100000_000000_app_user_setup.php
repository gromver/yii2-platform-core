<?php

use gromver\platform\core\modules\user\models\User;

class m100000_000000_app_user_setup extends yii\db\Migration
{
    public function up()
    {
        // Creates the default admin user
        $adminUser = new User();
        $adminUser->username = 'admin';
        $adminUser->status = User::STATUS_ACTIVE;

        echo 'Please type the admin user info: ' . PHP_EOL;
        $this->readStdinUser('Email (e.g. admin@mydomain.com)', $adminUser, 'email');
        $this->readStdinUser('Type Password', $adminUser, 'password', 'admin');

        if (!$adminUser->save()) {
            throw new \yii\console\Exception('Error when creating admin user.');
        }
        echo 'User created successfully.' . PHP_EOL;
    }

    public function down()
    {
        if($user = User::find()->where(['username'=>'admin'])->one()) {
            /** @var $user User */
            $user->delete();

            echo 'User "' . $user->username . '" deleted.';
        }
    }

    /**
     * @param string $prompt
     * @param \yii\base\Model $model
     * @param string $field
     * @param string $default
     * @return string
     */
    private function readStdinUser($prompt, $model, $field, $default = '')
    {
        while (!isset($input) || !$model->validate(array($field))) {
            echo $prompt . (($default) ? " [$default]" : '') . ': ';
            $input = (trim(fgets(STDIN)));
            if (empty($input) && !empty($default)) {
                $input = $default;
            }
            $model->$field = $input;
        }
        return $input;
    }
}
