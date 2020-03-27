<?php

namespace Plugin\PointsOnReferral\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class PointsOnReferralServiceProvider implements ServiceProviderInterface {

    public function register(BaseApplication $app) {
        self::registerMessage($app);

    }

    public function boot(BaseApplication $app) {

    }

    public static function registerMessage(BaseApplication $app) {
        $app['translator'] = $app->share(
            $app->extend(
                'translator',
                function ($translator, \Silex\Application $app) {
                    $translator->addLoader('yaml', new \Symfony\Component\Translation\Loader\YamlFileLoader());
                    $file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
                    if (file_exists($file)) {
                        $translator->addResource('yaml', $file, $app['locale']);
                    }

                    return $translator;
                }
            )
        );
    }

}
