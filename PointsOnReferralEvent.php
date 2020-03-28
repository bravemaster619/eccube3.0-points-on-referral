<?php

namespace Plugin\PointsOnReferral;

use Eccube\Event\EventArgs;
use Eccube\Event\TemplateEvent;
use Plugin\PointsOnReferral\Exception\UnprocessableEntityException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class PointsOnReferralEvent {

    /**
     * @var  \Eccube\Application $app
     */
    protected $app;

    protected $form;

    protected $config;
    /**
     * PointEvent constructor.
     * @param $app
     */
    public function __construct($app) {
        $this->app = $app;

    }

    public function init() {
        $this->config = $this->app['orm.em']->getRepository('Plugin\PointsOnReferral\Entity\PointsOnReferralConfig')->getConfig();
        $this->form = $this->app['form.factory']->createBuilder('admin_points_on_referral_config', $this->config)
            ->getForm();
    }

    public function onRouteAdminPointInfoController(FilterControllerEvent $event) {
        $this->init();
        $request = $event->getRequest();
        $PoRConfigRepository = $this->app['eccube.plugin.pointsonreferral.repository.config'];
        $form = $this->form;
        if ($request->isMethod("POST")) {
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    $PoRConfig = $form->getData();
                    $PoRConfigRepository->save($PoRConfig);
                } else {
                    $app = $this->app;
                    $message = $form->getErrorsAsString(true);
                    $app->error(function(\Exception $e, $code) use ($app, $message) {
                        if ($e instanceof UnprocessableEntityException) {
                            if ($message) {
                                $app->addError($message, 'admin');
                            }
                            return $app->redirect($app->url('point_info'));
                        }
                    });
                    throw new UnprocessableEntityException();
                }
            }
        }
    }

    public function onAdminPointInfoRender(TemplateEvent $event) {
        $parameters = $event->getParameters();
        $parts = $this->app['twig']->getLoader()->getSource('PointsOnReferral/Resource/template/admin/points_on_referral_config.twig');
        $search = '<div id="point_info_box" class="box accordion">';
        $replace = $parts . $search;
        $source = str_replace($search, $replace, $event->getSource());
        $event->setSource($source);
        $event->setParameters(array_merge($parameters, array(
            'por_form' => $this->form->createView()
        )));
    }

    public function onFrontEntryInitialize(EventArgs $event) {
        $request = $event->getRequest();
        $query_key = $this->app['config']['PointsOnReferral']['const']['referral_code_query_key'];
        $session_key = $this->app['config']['PointsOnReferral']['const']['session_key'];
        $referral_code = $request->get($query_key);
        if ($referral_code) {
            $this->app['session']->set($session_key, $referral_code);
        }
    }

    public function onFrontEntryComplete(EventArgs $event) {
        $session_key = $this->app['config']['PointsOnReferral']['const']['session_key'];
        $referral_code = $this->app['session']->get($session_key);
        $this->app['session']->remove($session_key);

        $Referee = $event->getArgument('Customer');
        $PoRReferee = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($this->app, $Referee);
        $this->app['orm.em']->persist($PoRReferee);
        $this->app['orm.em']->flush();

        if (!$referral_code) {
            return;
        }

        $PoRReferrer = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOneByReferralCode($referral_code);

        if (!$PoRReferrer) {
            return;
        }

        $Referrer = $this->app['eccube.repository.customer']->find($PoRReferrer->getCustomerId());

        if (!$Referrer) {
            return;
        }

        $PoRReferee->setReferrerId($Referrer->getId());
        $this->app['orm.em']->persist($PoRReferee);
        $this->app['orm.em']->flush();
        return;
    }

}
