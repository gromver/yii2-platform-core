<?php
/**
 * @link https://github.com/gromver/yii2-platform-core.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-core/blob/master/LICENSE
 * @package yii2-platform-core
 * @version 1.0.0
 */

namespace gromver\platform\core\modules\widget\widgets;


use gromver\models\ObjectModel;
use gromver\models\SpecificationInterface;
use gromver\platform\core\modules\widget\models\WidgetConfig;
use gromver\platform\core\modules\widget\widgets\assets\WidgetAsset;
use gromver\widgets\ModalIFrame;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * Class Widget
 * Базовый класс для фронтенд виджетов, предоставляет готовый интерфейс для настройки виджета администратором
 * Настройка осуществляется на основе публичных полей виджета и phpdoc'ов
 *
 * @package yii2-platform-core
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property $context string
 * @property $realContext string
 * @property $wrapperOptions array
 */
class Widget extends \yii\base\Widget implements SpecificationInterface
{
    /**
     * ID виджета, обязательный параметр
     * @var string
     */
    private $_id;
    /**
     * Используется для хранения первоначального конфига
     * @var array
     */
    private $_config;
    /**
     * Исключение перехваченное во время выполнения виджета, если режим отладки включен то исключения не перехватываются
     * @var \Exception
     */
    private $_exception;
    /**
     * Контекст настроек виджета, если не указан то используется Yii::$app->request->getPathInfo()
     * @var string
     */
    private $_context;
    /**
     * Контекст настроек виджета найденных в БД
     * @var string
     */
    private $_loadedContext;
    /**
     * Режим отладки, когда включен исключения не перехватываются
     * @var bool
     */
    private $_debug = false;
    /**
     * Отключение фазы self::init() во время создания объекта. Этот параметр используется при настройке виджетов,
     * для того чтобы отключить ненужную инициализацию виджета, во время которой могут быть искажены первоначальные настройки виджета,
     * это не критично при настройке виджета, но нежелательно. см. [[\gromver\platform\core\modules\widget\actions\ConfigureAction::run]]
     * @var bool
     */
    private $_skipInit = false;
    /**
     * Аттрибуты врапера виджета
     * @var array
     */
    private $_wrapperOptions = [];

    /**
     * Право доступа к кнопке настроек виджета
     * @var string
     */
    protected $_configureAccess = 'customizeWidget';
    /**
     * Компонент настройки виджетов, доступ к которому имеют пользователи с правом 'administrate'
     * Для настройки кастомных прав доступа к настройкам виджета нужно:
     * 1. Создать контроллер с экшеном \gromver\platform\core\modules\widget\actions\ConfigureAction и кастомным правом дуступа
     * 2. Настроить кастомное право доступа к кнопке настроек
     *      echo MyWidget::widget([
     *          ...
     *          'configureAccess' => 'newAccess'
     *      ]);
     * @var string
     */
    protected $_configureRoute = '/widget/backend/default/configure';
    /**
     * Право доступа к тексту пойманного исключения
     * @var string
     */
    protected $_exceptionAccess = 'administrate';

    public function __construct($config = [])
    {
        $this->_config = $config;

        try {
            if (!empty($config)) {
                Yii::configure($this, $config);
            }
            $this->preInit();
            if (!$this->_skipInit) {
                $this->init();
            }
        } catch(WidgetMissedIdException $e) {
            throw $e;
        } catch(\Exception $e) {
            $this->processException($e);
        }
    }

    /**
     * Обрабатывает исключение, перехватывая его либо пропуская, в зависимости от режима отладки
     * @param $e
     * @throws
     */
    private function processException($e)
    {
        if ($this->getDebug()) {
            throw $e;
        } else {
            $this->_exception = $e;
        }
    }

    /**
     * @throws WidgetMissedIdException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    protected function preInit()
    {
        if (!isset($this->id) || empty($this->id)) {
            throw new WidgetMissedIdException('Specify widget ' . __CLASS__ . '::id.');
        }

        if ($model = $this->findWidgetConfig()) {
            /** @var $model WidgetConfig */
            if ($model->widget_class != $this->className()) {
                throw new InvalidConfigException("DB's widget configuration is adjusted for a widget ". $model->widget_class . " that doesn't correspond to the current widget " . $this->className());
            }

            $this->_loadedContext = $model->context;

            foreach ($model->getParamsArray() as $key => $value) {
                if(!array_key_exists($key, $this->_config) && $this->hasProperty($key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * @return array|null|\yii\db\ActiveRecord
     */
    protected function findWidgetConfig()
    {
        $parts = empty($this->context) ? [''] : explode('/', '/' . $this->context);

        $contexts = []; $context = '';
        foreach ($parts as $part) {
            $context .= strlen($context) ? '/' . $part : $part;
            $contexts[] = $context;
        }

        return WidgetConfig::find()->orderBy('context desc')->where(['widget_id' => $this->id, 'context' => $contexts])->one();
    }

    /**
     * Вместо данного метода использовать метод [[self::launch]]
     * @return string|void
     * @throws
     */
    public function run()
    {
        $tag = ArrayHelper::remove($this->_wrapperOptions, 'tag', 'div');
        $this->_wrapperOptions['id'] = $this->id;
        Html::addCssClass($this->_wrapperOptions, 'widget-wrapper' . (Yii::$app->getIsEditMode() ? ' edit-mode' : ''));
        echo Html::beginTag($tag, $this->_wrapperOptions);

        if ($this->_exception === null) {
            try {
                $this->launch();
            } catch (\Exception $e) {
                $this->processException($e);
            }
        }

        if ($this->_exception && $this->hasExceptionAccess()) {
            $this->renderException();
        }

        if (Yii::$app->getIsEditMode()) {
            $this->renderControls();
        }

        echo Html::endTag($tag);

        WidgetAsset::register($this->getView());
    }

    /**
     * @throws \Exception в случае использования вьюхи, проверку на работоспособность виджета надо проводить до [[self::render()]],
     * т.к. если исключение сработает во вьюхе, то верстка нарушится
     */
    protected function launch() {}

    public function processSpecification(&$specification)
    {
        foreach ($this->_config as $attribute => $value) {
            if(array_key_exists($attribute, $specification)) $specification[$attribute]['disabled'] = true;
        }
    }

    /**
     * Отображает исключение, выброшенное при работе виджета
     */
    public function renderException()
    {
        echo Html::tag('p', Yii::t('gromver.platform', 'Widget error: {error}', ['error' => $this->_exception->getMessage()]), ['class' => 'text-danger widget-error']);
    }

    /**
     * Отображает кнопку настройки виджета и другие кастомные кнопки
     * @throws InvalidConfigException
     */
    public function renderControls()
    {
        echo Html::tag('div', $this->normalizeControls(array_merge($this->customControls(), [$this->widgetConfigControl()])), ['class' => 'widget-controls btn-group']);
        echo Html::tag('div', Yii::t('gromver.platform', 'Widget "{name}" (ID: {id})', ['name' => $this->name(), 'id' => $this->id]), ['class' => 'widget-description']);
    }

    /**
     * Массив с кастомными кнопками
     * @return array
     */
    public function customControls()
    {
        return [];
    }

    /**
     * @param $controls
     * @return string
     * @throws InvalidConfigException
     */
    protected function normalizeControls($controls)
    {
        $out = '';

        foreach ($controls as $item) {
            if (is_string($item)) {
                $out .= $item;
            } elseif (is_array($item)) {
                if (!isset($item['label'], $item['url'])) {
                    throw new InvalidConfigException('Control\'s label and url must be set.');
                }
                $options = array_merge(['class' => 'btn btn-default'], ArrayHelper::getValue($item, 'options', []));

                $out .= Html::a($item['label'], $item['url'], $options);
            }
        }

        return $out;
    }

    /**
     * @inheritdoc
     */
    public function getId($autoGenerate = true)
    {
        return $this->_id;
    }

    /**
     * @param string $value
     */
    public function setId($value)
    {
        $this->_id = $value;
    }

    /**
     * @return string
     */
    public function getContext()
    {
        if (!isset($this->_context)) {
            $this->_context = Yii::$app->request->getPathInfo();
        }

        return $this->_context;
    }

    /**
     * @param $value string
     */
    public function setContext($value)
    {
        $this->_context = $value;
    }

    /**
     * @return string
     */
    public function getLoadedContext()
    {
        return $this->_loadedContext;
    }

    /**
     * @param $value bool
     */
    public function setDebug($value)
    {
        $this->_debug = $value;
    }

    /**
     * @return bool
     */
    public function getDebug()
    {
        if(!isset($this->_debug))
            $this->_debug = !!YII_DEBUG;

        return $this->_debug;
    }

    /**
     * @param boolean $value
     */
    public function setSkipInit($value)
    {
        $this->_skipInit = $value;
    }

    /**
     * @return string
     */
    public function getExceptionAccess()
    {
        return $this->_exceptionAccess;
    }

    /**
     * @param string $exceptionAccess
     */
    public function setExceptionAccess($exceptionAccess)
    {
        $this->_exceptionAccess = $exceptionAccess;
    }

    /**
     * @return bool
     */
    public function hasExceptionAccess()
    {
        return $this->_exceptionAccess ? Yii::$app->user->can($this->_exceptionAccess) : true;
    }

    /**
     * @return string
     */
    public function getConfigureAccess()
    {
        return $this->_configureAccess;
    }

    /**
     * @param string $configureAccess
     */
    public function setConfigureAccess($configureAccess)
    {
        $this->_configureAccess = $configureAccess;
    }

    /**
     * @return bool
     */
    public function hasConfigureAccess()
    {
        return $this->_configureAccess ? Yii::$app->user->can($this->_configureAccess) : true;
    }

    /**
     * @return string
     */
    public function getConfigureRoute()
    {
        return $this->_configureRoute;
    }

    /**
     * @param string $configureRoute
     */
    public function setConfigureRoute($configureRoute)
    {
        $this->_configureRoute = $configureRoute;
    }

    /**
     * @return array
     */
    public function getWrapperOptions()
    {
        return $this->_wrapperOptions;
    }

    /**
     * @param array $wrapperOptions
     */
    public function setWrapperOptions($wrapperOptions)
    {
        $this->_wrapperOptions = $wrapperOptions;
    }

    /**
     * Помощник, для определения возвратный ссылки на этот виджет
     * @return string
     */
    public function getBackUrl()
    {
        $backUrl = Yii::$app->getRequest()->getUrl();
        $backUrl .= '#' . $this->getId();

        return $backUrl;
    }

    /**
     * Returns the list of attribute names.
     * By default, this method returns all public non-static properties of the class.
     * You may override this method to change the default behavior.
     * @return array list of attribute names.
     */
    public function attributes()
    {
        $class = new \ReflectionClass($this);
        $names = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic()) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    /**
     * Возвращает html кнопки настройки виджета, или false, если у пользователя нет доступа
     * @return string | bool
     */
    public function widgetConfigControl()
    {
        if (!$this->hasConfigureAccess()) {
            return false;
        }

        $objectModel = new ObjectModel($this->className());
        $objectModel->setAttributes($this->_config);

        return ModalIFrame::widget([
            'options' => [
                'class' => 'btn btn-default',
                'title' => Yii::t('gromver.platform', 'Configure widget'),
            ],
            'label' => '<i class="glyphicon glyphicon-cog"></i>',
            'url' => [$this->getConfigureRoute(), 'modal' => 1],
            'formOptions' => [
                'method' => 'post',
                'params' => [
                    'url' => Yii::$app->request->getAbsoluteUrl(),
                    'widget_id' => $this->id,
                    'widget_class' => $this->className(),
                    'widget_context' => $this->context,
                    'widget_config' => Json::encode($objectModel->toArray(array_keys($this->_config)))
                ]
            ]
        ]);
    }

    public function name()
    {
        $reflector = new \ReflectionClass($this);

        return Inflector::titleize($reflector->getShortName());
    }

    public function description()
    {
        return $this->className();
    }
}