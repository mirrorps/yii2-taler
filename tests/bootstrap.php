<?php

defined('YII_ENV') or define('YII_ENV', 'test');
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

// Boot a minimal Yii2 application for tests that need the DI container /
// component lifecycle (e.g. Component::init()).
new \yii\console\Application([
    'id'         => 'yii2-taler-tests',
    'basePath'   => __DIR__,
    'vendorPath' => __DIR__ . '/../vendor',
]);
