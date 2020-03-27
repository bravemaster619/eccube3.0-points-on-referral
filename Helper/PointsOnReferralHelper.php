<?php

namespace Plugin\PointsOnReferral\Helper;

use Eccube\Application;
use Eccube\Util\Str;

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
        $PoRCustomerRepo = $this->app['eccube.plugin.pointsonreferral.repository.customer'];
        do {
            $referral_code = strtoupper(Str::random(16) . time());
        } while($PoRCustomerRepo->findOneByReferralCode($referral_code));
        return $referral_code;
    }

}
