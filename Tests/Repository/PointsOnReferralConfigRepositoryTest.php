<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Plugin\PointsOnReferral\Entity\PointsOnReferralConfig;

class PointsOnReferralConfigRepositoryTest extends EccubeTestCase {

    public function testGetConfig() {
        $Config = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $this->assertNotNull($Config);
    }

    public function testSeed() {
        $Config = $this->app['eccube.plugin.pointsonreferral.repository.config']->seed();
        $this->assertEquals(true, $Config->getReferrerRewardsEnabled());
        $this->assertEquals(0, $Config->getReferrerRewards());
        $this->assertEquals(true, $Config->getRefereeRewardsEnabled());
        $this->assertEquals(0, $Config->getRefereeRewards());
        $Persisted = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $this->assertNotNull($Persisted);
        $this->assertEquals($Config->getReferrerRewardsEnabled(), $Persisted->getReferrerRewardsEnabled());
        $this->assertEquals($Config->getReferrerRewards(), $Persisted->getReferrerRewards());
        $this->assertEquals($Config->getRefereeRewardsEnabled(), $Persisted->getRefereeRewardsEnabled());
        $this->assertEquals($Config->getRefereeRewards(), $Persisted->getRefereeRewards());
    }

    public function testSave() {
        $Config = new PointsOnReferralConfig();
        $Config->setReferrerRewardsEnabled(true)
            ->setReferrerRewards(1500)
            ->setRefereeRewardsEnabled(false)
            ->setRefereeRewards(500);
        $this->app['eccube.plugin.pointsonreferral.repository.config']->save($Config);
        $Persisted = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $this->assertNotNull($Persisted);
        $this->assertEquals(true, $Persisted->getReferrerRewardsEnabled());
        $this->assertEquals(1500, $Persisted->getReferrerRewards());
        $this->assertEquals(false, $Persisted->getRefereeRewardsEnabled());
        $this->assertEquals(500, $Persisted->getRefereeRewards());
    }

}
