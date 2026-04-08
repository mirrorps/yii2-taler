<?php

namespace mirrorps\Yii2Taler;

use yii\base\Application;
use yii\base\BootstrapInterface;

/**
 * Bootstrap class — registers the extension with Yii's dependency injection
 * container so the `taler` component alias resolves automatically when the
 * component is defined in application config.
 */
class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        if (!$app->has('taler')) {
            $app->set('taler', [
                'class' => Taler::class,
            ]);
        }
    }
}
