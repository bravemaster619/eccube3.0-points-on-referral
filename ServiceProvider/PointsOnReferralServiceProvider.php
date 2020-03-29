<?php

namespace Plugin\PointsOnReferral\ServiceProvider;

use Eccube\Common\Constant;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\ProcessIdProcessor;
use Monolog\Processor\WebProcessor;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;
use Symfony\Bridge\Monolog\Logger;

class PointsOnReferralServiceProvider implements ServiceProviderInterface {

    public function register(BaseApplication $app) {

        // register routes
        $front = $app['controllers_factory'];
        if ($app['config']['force_ssl'] == Constant::ENABLED) {
            $front->requireHttps();
        }
        $front->match('/mypage/referral', 'Plugin\PointsOnReferral\Controller\PointsOnReferralMyPageController::index')->bind('mypage_referral');
        $app->mount('', $front);

        self::registerTranslator($app);

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
        $app['eccube.plugin.pointsonreferral.repository.history'] = $app->share(
            function() use ($app) {
                return $app['orm.em']->getRepository('Plugin\PointsOnReferral\Entity\PointsOnReferralHistory');
            }
        );
        // register form type
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\PointsOnReferral\Form\Type\PointsOnReferralConfigType($app);
            return $types;
        }));
        // register logger
        if (!method_exists('Eccube\Application', 'getInstance')) {
            eccube_log_init($app);
        }

        $app['monolog.logger.points_on_referral'] = $this->initLogger($app, array(
            'name' => 'plugin.points_on_referral',
            'filename' => 'points_on_referral',
            'delimiter' => '_',
            'dateformat' => 'Y-m-d',
            'max_files' => '90',
            'log_dateformat' => 'Y-m-d H:i:s,u',
        ));
    }

    public function boot(BaseApplication $app) {

    }

    public static function registerTranslator(BaseApplication $app) {
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

    public function initLogger($app, array $option) {
        return $app->share(function ($app) use($option) {
            $logger = new $app['monolog.logger.class']($option['name']);
            $file = $app['config']['root_dir'].'/app/log/'.$option['filename'].'.log';
            $RotateHandler = new RotatingFileHandler($file, $option['max_files'], Logger::INFO);
            $RotateHandler->setFilenameFormat(
                $option['filename'].'_{date}',
                $option['dateformat']
            );
            $token = substr($app['session']->getId(), 0, 8);
            $format = "[%datetime%] [".$token."] %channel%.%level_name%: %message% %context% %extra%\n";
            $RotateHandler->setFormatter(new LineFormatter($format));
            $logger->pushHandler(
                new FingersCrossedHandler(
                    $RotateHandler,
                    new ErrorLevelActivationStrategy(Logger::INFO)
                )
            );
            $logger->pushProcessor(function ($record) {
                // 出力ログからファイル名を削除し、lineを最終項目にセットしなおす
                unset($record['extra']['file']);
                $line = $record['extra']['line'];
                unset($record['extra']['line']);
                $record['extra']['line'] = $line;

                return $record;
            });

            $ip = new IntrospectionProcessor();
            $logger->pushProcessor($ip);

            $web = new WebProcessor();
            $logger->pushProcessor($web);

            // $uid = new UidProcessor(8);
            // $logger->pushProcessor($uid);

            $process = new ProcessIdProcessor();
            $logger->pushProcessor($process);


            return $logger;
        });
    }

}
