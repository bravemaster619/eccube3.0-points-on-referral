<?php
namespace Plugin\PointsOnReferral;

use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Plugin\AbstractPluginManager;
use Plugin\PointsOnReferral\Exception\DependencyNotFoundException;
use Plugin\PointsOnReferral\ServiceProvider\PointsOnReferralServiceProvider;
use Symfony\Component\Filesystem\Filesystem;

class PluginManager extends AbstractPluginManager {

    public function install($config, $app) {

    }

    public function uninstall($config, $app) {
        $this->rollbackMigration($config, $app);
        $this->removeResources($config, $app);
        $this->unregisterPage($config, $app);
    }

    public function enable($config, $app) {
        // check if Point plugin is installed
        $this->checkDependency($config, $app);
        $this->copyResources($config, $app);
        $this->migrateDb($config, $app);
        $this->registerPage($config, $app);
    }

    public function disable($config, $app) {

    }

    protected function checkDependency($config, $app) {
        if (!$this->checkInstallPlugin($app, 'Point')) {
            $app->error(function(\Exception $e, $code) use ($app) {
                if ($e instanceof DependencyNotFoundException) {
                    PointsOnReferralServiceProvider::registerTranslator($app);
                    $app->addError($app['translator']->trans('admin.plugin.enable.error.point_not_found'), 'admin');
                    return $app->redirect($app->url('admin_store_plugin'));
                }
            });
            throw new DependencyNotFoundException();
        }
    }


    protected function migrateDb($config, $app) {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code']);
        $PoRConfigRepository = $app['orm.em']->getRepository('Plugin\PointsOnReferral\Entity\PointsOnReferralConfig');
        // check if the configuration exists
        $Config = $PoRConfigRepository->getConfig();
        if (!$Config) {
            // if the configuration does not exist, seed the default one
            $PoRConfigRepository->seed();
        }
        $PoRCustomerRepository = $app['orm.em']->getRepository('Plugin\PointsOnReferral\Entity\PointsOnReferralCustomer');
        $PoRCustomerRepository->updateAll($app);
    }

    protected function rollbackMigration($config, $app) {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code'], 0);
    }


    protected function copyResources($config, $app) {
        $file = new Filesystem();
        $file->copy(
            __DIR__ . '/Resource/assets/css/admin.css',
            $app['config']['plugin_html_realdir'] . "/" . $config['code'] . "/admin.css"
        );
        $file->copy(
            __DIR__ . '/Resource/assets/css/default.css',
            $app['config']['plugin_html_realdir'] . "/" . $config['code'] . "/default.css"
        );
        $file->copy(
            __DIR__ . '/Resource/assets/css/admin.min.css',
            $app['config']['plugin_html_realdir'] . "/" . $config['code'] . "/admin.min.css"
        );
        $file->copy(
            __DIR__ . '/Resource/assets/css/default.min.css',
            $app['config']['plugin_html_realdir'] . "/" . $config['code'] . "/default.min.css"
        );
        $file->copy(
            __DIR__ . '/Resource/template/default/MyPage/referral.twig',
            $app['config']['template_realdir'] . "/Mypage/referral.twig"
        );
    }

    protected function removeResources($config, $app) {
        $file = new Filesystem();
        $file->remove($app['config']['plugin_html_realdir'] . "/" . $config['code']);
        $file->remove($app['config']['template_realdir'] . "/Mypage/referral.twig");
    }

    protected function registerPage($config, $app) {
        $PageLayout = $app['eccube.repository.page_layout']->findOneBy(array(
            'url' => 'mypage_referral',
            'file_name' => 'MyPage/referral'
        ));
        $DeviceType = $app['orm.em']->getRepository('\Eccube\Entity\Master\DeviceType')->find(DeviceType::DEVICE_TYPE_PC);
        if (!$PageLayout) {

            $PageLayout = $app['eccube.repository.page_layout']->newPageLayout($DeviceType);
            $PageLayout->setCreateDate(date_create());
        }
        $PageLayout->setUrl('mypage_referral')
            ->setFileName('Mypage/referral')
            ->setName('MYページ/' . $config['name'])
            ->setDeviceType($DeviceType)
            ->setEditFlg(PageLayout::EDIT_FLG_DEFAULT)
            ->setUpdateDate(date_create());
        $app['orm.em']->persist($PageLayout);
        $app['orm.em']->flush();
    }

    protected function unregisterPage($config, $app) {
        $PageLayout = $app['eccube.repository.page_layout']->findOneBy(array(
            'url' => 'mypage_referral',
            'file_name' => 'Mypage/referral'
        ));
        if ($PageLayout) {
            $app['orm.em']->remove($PageLayout);
            $app['orm.em']->flush();
        }
    }

    protected function checkInstallPlugin($app, $code) {
        $Plugin = $app['eccube.repository.plugin']->findOneBy(array('code' => $code, 'enable' => 1));
        if($Plugin){
            return true;
        }else{
            return false;
        }
    }

}
