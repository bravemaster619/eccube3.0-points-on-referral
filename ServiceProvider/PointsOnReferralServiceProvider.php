<?php

namespace Plugin\PointsOnReferral\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class PointsOnReferralServiceProvider implements ServiceProviderInterface {

    public function register(BaseApplication $app) {
        self::registerMessage($app);

        // register repositories
        $app['eccube.plugin.pointsonreferral.repository.config'] = $app->share(
            function() use ($app) {
                return $app['orm.em']->getRepository('Plugin\PointsOnReferral\Entity\PointsOnReferralConfig');
            }
        );
        $app['eccube.plugin.pointsonreferral.repository.customer'] = $app->share(
            function() use ($app) {
                return $app['orm.em']->getRepository('Plugin\PointsOnReferral\Entity\PointsOnReferralCustomer');
            }
        );
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
