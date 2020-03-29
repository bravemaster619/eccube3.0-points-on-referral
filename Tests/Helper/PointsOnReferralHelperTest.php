<?php

namespace Eccube\Tests\Helper;

use Eccube\Tests\EccubeTestCase;
use Plugin\PointsOnReferral\Helper\PointsOnReferralHelper;
use Plugin\PointsOnReferral\Helper\PointsOnReferralNavigationHelper;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PointsOnReferralHelperTest extends EccubeTestCase {

    public function testGenerateReferralCode() {
        $PoRHelper = new PointsOnReferralHelper($this->app);
        $referral_code = $PoRHelper->generateReferralCode();
        $this->assertNotEmpty($referral_code);
    }

}
