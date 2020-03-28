<?php

namespace Plugin\PointsOnReferral\Helper;

use Eccube\Application;
use Eccube\Entity\Customer;
use Eccube\Util\Str;
use Plugin\Point\Entity\Point;
use Plugin\Point\Entity\PointSnapshot;
use Plugin\Point\Helper\PointHistoryHelper\PointHistoryHelper;
use Plugin\PointsOnReferral\Entity\PointsOnReferralHistory;

class PointsOnReferralHelper {

    /**
     * @var Application
     */
    private $app;

    /**
     * PointsOnReferralHelper constructor.
     * @param $app Application
     */
    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * @return string
     */
    public function generateReferralCode() {
        $PoRCustomerRepo = $this->app['orm.em']->getRepository('Plugin\PointsOnReferral\Entity\PointsOnReferralCustomer');
        do {
            $referral_code = strtoupper(Str::random(16) . time());
        } while($PoRCustomerRepo->findOneByReferralCode($referral_code));
        return $referral_code;
    }

    /**
     * @param $Customer Customer
     * @param $point integer
     * @param $ownership integer
     */
    public function addRewards(Customer $Customer, $point, $ownership) {
        if ($point < 0.1) {
            return;
        }
        $pointCurrent = $this->app['eccube.plugin.point.repository.pointcustomer']->getLastPointById($Customer->getId());
        $pointCurrent = $pointCurrent + $point;

        $saveEntity = $this->app['eccube.plugin.point.repository.pointcustomer']->savePoint($pointCurrent, $Customer);
        $PointSnapshot = new PointSnapshot();
        $actionName = '';
        if ($ownership === PointsOnReferralHistory::REFERRER) {
            $actionName = $this->app['translator']->trans('history.action.rewards.referrer');
        } else if ($ownership === PointsOnReferralHistory::REFEREE) {
            $actionName = $this->app['translator']->trans('history.action.rewards.referee');
        }
        $PointSnapshot->setCustomer($Customer)
            ->setCustomerId($Customer->getId())
            ->setPlgPointAdd($point)
            ->setPlgPointCurrent($pointCurrent)
            ->setPlgPointUse(0)
            ->setCreateDate(date_create())
            ->setUpdateDate(date_create())
            ->setPlgPointSnapActionName($actionName);
        $this->app['orm.em']->persist($PointSnapshot);
        $this->app['orm.em']->flush();

        $Point = new Point();
        $Point->setPointInfo($this->app['eccube.plugin.point.repository.pointinfo']->getLastInsertData())
            ->setCustomer($Customer)
            ->setPlgDynamicPoint($point)
            ->setPlgPointType(PointHistoryHelper::HISTORY_MESSAGE_TYPE_ADD)
            ->setPlgPointActionName(PointHistoryHelper::HISTORY_MESSAGE_TYPE_ADD . $actionName)
            ->setCreateDate(date_create())
            ->setUpdateDate(date_create());
        $this->app['orm.em']->persist($Point);
        $this->app['orm.em']->flush();
    }

}
