<?php

use Eccube\Tests\EccubeTestCase;
use \Plugin\PointsOnReferral\Entity\PointsOnReferralHistory;

class PointsOnReferralHistoryTest extends EccubeTestCase {

    public function testGetOwnerShip() {
        $Referrer = $this->createCustomer();
        $Referee = $this->createCustomer();
        $PoRConfig = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->create($Referrer, $Referee, $PoRConfig);
        $this->assertEquals(PointsOnReferralHistory::REFERRER, $PoRHistory->getOwnerShip($Referrer));
        $this->assertEquals(PointsOnReferralHistory::REFEREE, $PoRHistory->getOwnerShip($Referee));
        $this->assertEquals(PointsOnReferralHistory::UNKNOWN, $PoRHistory->getOwnerShip($this->createCustomer()));
    }

    public function testReadByAndHasRead() {
        $Referrer = $this->createCustomer();
        $Referee = $this->createCustomer();
        $PoRConfig = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->create($Referrer, $Referee, $PoRConfig);
        $this->assertNull($PoRHistory->getReferrerReadDate());
        $this->assertFalse($PoRHistory->hasRead($Referrer));
        $this->assertFalse($PoRHistory->hasRead($Referee));
        // read by referrer
        $PoRHistory->readBy($Referrer);
        $this->assertTrue($PoRHistory->hasRead($Referrer));
        $this->assertFalse($PoRHistory->hasRead($Referee));
        // read by referee
        $PoRHistory->readBy($Referee);
        $this->assertTrue($PoRHistory->hasRead($Referee));
        $this->assertFalse($PoRHistory->hasRead($this->createCustomer()));
    }

}
