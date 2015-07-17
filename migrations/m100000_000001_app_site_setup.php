<?php

class m100000_000001_app_site_setup extends \yii\db\Migration
{
    public function up()
    {
        // Create folders for media manager
        $webroot = Yii::getAlias('@app/web');
        foreach (['upload', 'files'] as $folder) {
            $path = $webroot . '/' . $folder;
            if (!file_exists($path)) {
                echo "mkdir('$path', 0777)...";
                if (mkdir($path, 0777, true)) {
                    echo "done.\n";
                } else {
                    echo "failed.\n";
                }
            }
        }
        // Creates the default platform config
        /** @var \gromver\platform\core\modules\main\models\MainParams $params */
        $params = Yii::$app->paramsManager->main;

        $model = new \gromver\models\ObjectModel(\gromver\platform\core\modules\main\models\MainParams::className());
        $model->setAttributes($params->toArray());

        echo 'Setup application config: ' . PHP_EOL;
        $this->readStdinUser('Site Name (My Site)', $model, 'siteName', 'My Site');
        $this->readStdinUser('Admin Email (admin@email.com)', $model, 'adminEmail', 'admin@email.com');
        $this->readStdinUser('Support Email (support@email.com)', $model, 'supportEmail', 'support@email.com');

        if ($model->validate()) {
            \gromver\platform\core\modules\main\models\MainParams::create($model->toArray())->save();
        }

        echo 'Setup complete.' . PHP_EOL;
    }

    /*public function down()
    {
        echo "m141128_060147_cmf_site_setup cannot be reverted.\n";

        return false;
    }*/

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
