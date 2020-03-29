<?php

namespace Plugin\PointsOnReferral\Controller;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

class PointsOnReferralMyPageController {

    public function index(Application $app, Request $request) {
        if (!$app->isGranted('ROLE_USER')) {
            return $app->redirect($app->url('mypage_login'));
        }
        $Customer = $app['user'];
        $PoRCustomer = $app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($app, $Customer);
//        $HistoriyList = $app['eccube.plugin.pointsonreferral.repository.history']->findReferralsByCustomer($Customer);
        $qb = $app['eccube.plugin.pointsonreferral.repository.history']->getQueryBuilderByCustomer($Customer);
        $pagination = $app['paginator']()->paginate(
            $qb,
            $request->get('pageno', 1),
            $app['config']['search_pmax']
        );
        return $app->renderView('PointsOnReferral/Resource/template/default/MyPage/referral.twig', array(
            'HistoryList' => array(),
            'pagination' => $pagination,
            'PoRCustomer' => $PoRCustomer
        ));
//        return $app->renderView('MyPage/referral.twig');
    }

}
