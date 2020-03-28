<?php


namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;

class PointsOnReferralHistoryRepositoryTest extends EccubeTestCase {

    public function testCreate() {
        $Referrer = $this->createCustomer();
        $Referee = $this->createCustomer();
        $PoRConfig = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->create($Referrer, $Referee, $PoRConfig);
        // check if info are correctly set
        $this->assertEquals($Referrer->getId(), $PoRHistory->getReferrerId());
        $this->assertEquals($Referrer->getEmail(), $PoRHistory->getReferrerEmail());
        $this->assertEquals($Referrer->getName01() . " " . $Referrer->getName02(), $PoRHistory->getReferrerFullName());
        $this->assertEquals($Referrer->getKana01() . " " . $Referrer->getKana02(), $PoRHistory->getReferrerFullKana());
        $this->assertEquals($Referee->getId(), $PoRHistory->getRefereeId());
        $this->assertEquals($Referee->getEmail(), $PoRHistory->getRefereeEmail());
        $this->assertEquals($Referee->getName01() . " " . $Referee->getName02(), $PoRHistory->getRefereeFullName());
        $this->assertEquals($Referee->getKana01() . " " . $Referee->getKana02(), $PoRHistory->getRefereeFullKana());
        // when referrer reward is disabled
        $PoRConfig->setReferrerRewardsEnabled(false)
            ->setReferrerRewards(1000)
            ->setRefereeRewardsEnabled(true)
            ->setRefereeRewards(333);
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->create($Referrer, $Referee, $PoRConfig);
        $this->assertEquals(0, $PoRHistory->getReferrerRewards());
        $this->assertEquals(333, $PoRHistory->getRefereeRewards());
        // when referee reward is disabled
        $PoRConfig->setReferrerRewardsEnabled(true)
            ->setReferrerRewards(500)
            ->setRefereeRewardsEnabled(false)
            ->setRefereeRewards(400);
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->create($Referrer, $Referee, $PoRConfig);
        $this->assertEquals(500, $PoRHistory->getReferrerRewards());
        $this->assertEquals(0, $PoRHistory->getRefereeRewards());
        // when both rewards are enabled
        $PoRConfig->setReferrerRewardsEnabled(true)
            ->setReferrerRewards(2000)
            ->setRefereeRewardsEnabled(true)
            ->setRefereeRewards(1500);
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->create($Referrer, $Referee, $PoRConfig);
        $this->assertEquals(2000, $PoRHistory->getReferrerRewards());
        $this->assertEquals(1500, $PoRHistory->getRefereeRewards());
    }

}
