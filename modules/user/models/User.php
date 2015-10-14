<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\user\models;


use gromver\modulequery\ModuleEvent;
use gromver\platform\core\modules\user\models\events\BeforeRolesSaveEvent;
use Yii;
use yii\base\ModelEvent;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Json;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "grom_user".
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 * @property integer $last_visit_at
 * @property integer $login_ip
 *
 * @property string[] $roles
 * @property bool $isSuperAdmin
 * @property bool $isTrashed
 * @property UserParam[] $params
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_LOGIN = 'login';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_RESET_PASSWORD = 'resetPassword';

    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_SUSPENDED = 3;

    const EVENT_BEFORE_SAFE_DELETE = 'beforeSafeDelete';
    const EVENT_AFTER_SAFE_DELETE = 'afterSafeDelete';
    const EVENT_BEFORE_USER_ROLES_SAVE = 'beforeUserRolesSave';

    const EVENT_BEFORE_TRASH = 'beforeSafeDelete';
    const EVENT_AFTER_TRASH = 'afterSafeDelete';

    /**
     * @var string the raw password. Used to collect password input and isn't saved in database
     */
    public $password;
    /**
     * @var string the raw password confirmation. Used to check password input and isn't saved in database
     */
    public $passwordConfirm;

    private $_isSuperAdmin = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%core_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            [['status', 'created_at', 'updated_at', 'deleted_at', 'last_visit_at', 'login_ip'], 'integer'],
            [['email', 'password_hash', 'auth_key'], 'string', 'max' => 128],
            [['password_reset_token'], 'string', 'max' => 32],
            [['username'], 'string', 'max' => 64],

            [['status'], 'default', 'value' => static::STATUS_ACTIVE],
            [['status'], 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_SUSPENDED]],

            [['username'], 'filter', 'filter' => 'trim'],
            [['username'], 'required'],
            [['username'], 'unique', 'message' => Yii::t('gromver.platform', 'This username has already been taken.')],
            [['username'], 'string', 'min' => 2, 'max' => 255],

            [['email'], 'filter', 'filter' => 'trim'],
            [['email'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique', 'message' => Yii::t('gromver.platform', 'This email address has already been taken.')],
            [['password'], 'required', 'on' => $this::SCENARIO_CREATE],
            [['password'], 'string', 'max' => 128],
            [['passwordConfirm'], 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'on' => [$this::SCENARIO_CREATE, $this::SCENARIO_UPDATE, $this::SCENARIO_RESET_PASSWORD]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'username' => Yii::t('gromver.platform', 'Username'),
            'email' => Yii::t('gromver.platform', 'Email'),
            'password' => Yii::t('gromver.platform', 'Password'),
            'password_hash' => Yii::t('gromver.platform', 'Password Hash'),
            'password_reset_token' => Yii::t('gromver.platform', 'Password Reset Token'),
            'password_new' => Yii::t('gromver.platform', 'New Password'),
            'password_confirm' => Yii::t('gromver.platform', 'Confirm Password'),
            'auth_key' => Yii::t('gromver.platform', 'Auth Key'),
            'status' => Yii::t('gromver.platform', 'Status'),
            'roles' => Yii::t('gromver.platform', 'Roles'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
            'updated_at' => Yii::t('gromver.platform', 'Updated At'),
            'deleted_at' => Yii::t('gromver.platform', 'Deleted At'),
            'last_visit_at' => Yii::t('gromver.platform', 'Last Visit At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            $this::SCENARIO_CREATE => ['username', 'email', 'password', 'passwordConfirm', 'status', 'roles'],
            $this::SCENARIO_LOGIN => ['last_visit_time', 'login_ip'],
            $this::SCENARIO_UPDATE => ['status', 'roles', 'password', 'passwordConfirm'],
            $this::SCENARIO_RESET_PASSWORD => ['password', 'passwordConfirm'],
        ] + parent::scenarios();
    }

    /**
     * @inheritdoc
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                    self::EVENT_BEFORE_SAFE_DELETE => 'deleted_at',
                ]
            ]
        ] + Yii::$app->userBehaviors;
    }

    // status label
    private static $_statuses = [
        self::STATUS_SUSPENDED => 'Suspended',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public function getStatusLabel($status = null)
    {
        if ($status === null) {
            return Yii::t('gromver.platform', self::$_statuses[$this->status]);
        }

        return Yii::t('gromver.platform', self::$_statuses[$status]);
    }

    public static function statusLabels()
    {
        return array_map(function ($label) {
                return Yii::t('gromver.platform', $label);
            }, self::$_statuses);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::find()->active()->andWhere(['id' => $id])->one();
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return null|User
     */
    public static function findByUsername($username)
    {
        return static::find()->active()->andWhere(['username' => $username])->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Yii::$app->getModule('auth')->passwordResetTokenExpire;
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
                'password_reset_token' => $token,
                'status' => self::STATUS_ACTIVE,
            ]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!empty($this->password)) {
                $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
                $this->password_reset_token = null;
            }

            if ($insert) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // сохраняем/удаляем роли
        if(is_array($this->_roles)) {
            ModuleEvent::trigger(self::EVENT_BEFORE_USER_ROLES_SAVE, new BeforeRolesSaveEvent([
                'sender' => $this
            ]));
            $newRoles = $this->_roles;
            $this->_roles = null;

            $oldRoles = $this->getRoles();

            $toAssign = array_diff($newRoles, $oldRoles);
            $toRevoke = array_diff($oldRoles, $newRoles);

            $auth = Yii::$app->authManager;

            foreach($toAssign as $role) {
                $auth->assign($auth->getRole($role), $this->id);
            }

            foreach($toRevoke as $role) {
                $auth->revoke($auth->getRole($role), $this->id);
            }
        }


        // сохраняем/удаляем параметры пользователя
        $params = $this->params;
        foreach ($params as $name => $param) {
            if (is_null($param->value)) {
                if (!$param->isNewRecord) {
                    // удаляем параметр из БД
                    $param->delete();
                }
            } else {
                $param->user_id = $this->id;
                $param->save();
            }
        }
    }

    public function trash()
    {
        if ($this->beforeTrash()) {
            if ($this->save(false)) {
                $this->afterTrash();
            }
        } else {
            return false;
        }

        return true;
    }

    public function untrash()
    {
        $this->deleted_at = null;
        return $this->save(false);
    }

    public function getIsTrashed()
    {
        return !is_null($this->deleted_at);
    }

    public function beforeTrash()
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_TRASH, $event);

        return $event->isValid;
    }

    public function afterTrash()
    {
        $this->trigger(self::EVENT_AFTER_TRASH);
    }

    private $_roles;

    public function setRoles($value)
    {
        $roles = array_keys(Yii::$app->authManager->getRoles());
        $this->_roles = array_intersect((array)$value, $roles);
    }

    public function getRoles()
    {
        if (!isset($this->_roles)) {
            $this->_roles = array_keys(Yii::$app->authManager->getRolesByUser($this->id));
        }

        return $this->_roles;
    }

    public function getParams()
    {
        return self::hasMany(UserParam::className(), ['user_id' => 'id'])->indexBy('name');
    }

    public function setParams($params)
    {
        $remove = array_diff(array_keys($this->params), array_keys($params));

        foreach ($params as $name => $value) {
            $this->setParam($name, $value);
        }

        foreach ($remove as $name) {
            $this->setParam($name, null);
        }
    }

    /**
     * @param $name string
     * @param $value mixed любое приводимое к строке значение,
     * если указан null то при сохранении пользователя параметр будет удален из БД
     * @throws \yii\base\InvalidConfigException
     */
    public function setParam($name, $value)
    {
        /** @var UserParam[] $params */
        $params = $this->params;

        if (array_key_exists($name, $params) && ($p = $params[$name]) instanceof UserParam) {
            $p->value = (string)$value;
        } else {
            $params[$name] = Yii::createObject([
                'class' => UserParam::className(),
                'user_id' => $this->id,
                'name' => $name,
                'value' => (string)$value
            ]);
        }

        $this->populateRelation('params', $params);
    }

    /**
     * @param $name string
     * @param mixed $default
     * @return null|string
     */
    public function getParam($name, $default = null)
    {
        return isset($this->params[$name]) ? $this->params[$name]->value : $default;
    }

    /**
     * @param string $name
     * @param integer $relationId
     * @param mixed $default
     * @return null|string
     */
    public function getRelativeParam($name, $relationId, $default = null)
    {
        return $this->getParam("{$name}_{$relationId}", $default);
    }

    /**
     * @param string $name
     * @param integer $relationId
     * @param mixed $value
     */
    public function setRelativeParam($name, $relationId, $value)
    {
        $this->setParam("{$name}_{$relationId}", $value);
    }


    /**
     * Returns whether the logged in user is an administrator.
     *
     * @return boolean the result.
     */
    public function getIsSuperAdmin()
    {
        if ($this->_isSuperAdmin !== null) {
            return $this->_isSuperAdmin;
        }

        $this->_isSuperAdmin = in_array($this->username, Yii::$app->user->superAdmins);
        return $this->_isSuperAdmin;
    }

    /**
     * @param $permissionName
     * @param array $params
     * @return bool
     */
    public function can($permissionName, $params = [])
    {
        return $this->getIsSuperAdmin() ? true : Yii::$app->authManager->checkAccess($this->id, $permissionName, $params);
    }
}
