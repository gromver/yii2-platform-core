Grom Platform
=============
Платформа для разработки веб приложений, на основе Yii2 Basic application template

## Демо сайт
http://demo.gromver.com

## Возможности

* Модули: авторизация, пользователи, меню, страницы, новости, теги, поиск, медиа менеджер и т.д.
* Древовидные категории новостей.
* Встроенная система контроля версий документов.
* Поиск
* SEO-friendly адреса страниц (ЧПУ)

Установка
------------

Через [composer](http://getcomposer.org/download/).

Запустить в командной строке проекта

```
php composer.phar require --prefer-dist gromver/yii2-platform-core "*"
```

или добавить

```
"gromver/yii2-platform-core": "*"
```

в require секцию `composer.json` файла.


#### Настройка Grom Platform
Заменяем веб и консольное приложения на соответсвующие из данного расширения. Для этого правим файлы:

* /web/index.php
```
  (new \gromver\platform\core\Application($config))->run();  //(new yii\web\Application($config))->run();
```
* /yii.php
```
  $application = new \gromver\platform\core\console\Application($config);  //yii\console\Application($config);
```

Нужно отредактировать конфиг приложения: /config/web.php

``` 
[
  'components' => [
      'user' => [
          //'identityClass' => 'app\models\User',  //закоментировать или удалить эту строку
          'enableAutoLogin' => true,
      ],
    ]
]
```
#### Создание таблиц, папок и первоначальных настроек приложения
Для начала нужно убедится, что в корне приложения создана папка migrations, иначе будет ошибка
Error: Migration failed. Directory specified in migrationPath doesn't exist.

    php yii core-migrate

В результате применения миграций будут добавлены папки
 * /web/upload  - для хранения изображений прикрепляемых к статьям и категориям
 * /web/files   - для хранения файлов медиа менеджера
