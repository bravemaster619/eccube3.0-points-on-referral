<?php
namespace Plugin\PointsOnReferral;

use Eccube\Plugin\AbstractPluginManager;
use Plugin\PointsOnReferral\Exception\DependencyNotFoundException;
use Plugin\PointsOnReferral\ServiceProvider\PointsOnReferralServiceProvider;
use Plugin\PointsOnSignup\Service\UtilService;

class PluginManager extends AbstractPluginManager {

    public function install($config, $app) {

    }

    public function uninstall($config, $app) {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code'], 0);
    }

    public function enable($config, $app) {
        // check if Point plugin is installed
        $UtilService = new UtilService($app);
        if (!$UtilService->checkInstallPlugin('Point')) {
            $app->error(function(\Exception $e, $code) use ($app) {
                if ($e instanceof DependencyNotFoundException) {
                    PointsOnReferralServiceProvider::registerMessage($app);
                    $app->addError($app['translator']->trans('admin.plugin.enable.error.point_not_found'), 'admin');
                    return $app->redirect($app->url('admin_store_plugin'));
                }
            });
            throw new DependencyNotFoundException();
        }
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code']);
    }

    public function disable($config, $app) {

    }

}
