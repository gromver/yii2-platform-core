<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\components;


/**
 * Class User
 * @property \gromver\platform\core\modules\user\models\User $identity The identity object associated with the currently logged user. Null
 * is returned if the user is not logged in (not authenticated).
 *
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class User extends \yii\web\User
{
    /**
	 * @inheritdoc
	 */
	public $identityClass = 'gromver\platform\core\modules\user\models\User';

	/**
	 * @inheritdoc
	 */
	public $enableAutoLogin = true;

	/**
	 * @inheritdoc
	 */
	public $loginUrl = ['/auth/default/login'];

    public $superAdmins = ['admin'];

    public $defaultRoles = ['authorized'];

	/**
	 * @inheritdoc
	 */
	protected function afterLogin($identity, $cookieBased, $duration)
	{
		parent::afterLogin($identity, $cookieBased, $duration);
		$this->identity->setScenario(self::EVENT_AFTER_LOGIN);
		$this->identity->last_visit_at = time();
		$this->identity->login_ip = ip2long(\Yii::$app->getRequest()->getUserIP());
		$this->identity->save(false);
	}

	public function getIsSuperAdmin()
	{
		if ($this->isGuest) {
			return false;
		}
		return $this->identity->getIsSuperAdmin();
	}

    /**
     * Если пользователь неавторизован - всегда false, иначе yii не кеширует и начинает спамить одни и теже запросы в бд
     * @inheritdoc
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        // Always return true when SuperAdmin user
        if ($this->getIsSuperAdmin()) {
            return true;
        }
        return $this->getIsGuest() ? false : parent::can($permissionName, $params, $allowCaching);
    }
}