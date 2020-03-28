<?php


namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;

class PointsOnReferralHistoryRepositoryTest extends EccubeTestCase {

    public function testCreate() {
        $Referrer = $this->createCustomer();
        $Referee = $this->createCustomer();
        $PoRConfig = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->create($Referrer, $Referee, $PoRConfig);
        $this->assertEquals($Referrer->getName01() . " " . $Referrer->getName02(), $PoRHistory->getReferrerFullName());
        $this->assertEquals($Referrer->getKana01() . " " . $Referrer->getKana02(), $PoRHistory->getReferrerFullKana());
        $this->assertEquals($Referee->getName01() . " " . $Referee->getName02(), $PoRHistory->getRefereeFullName());
        $this->assertEquals($Referee->getKana01() . " " . $Referee->getKana02(), $PoRHistory->getRefereeFullKana());
    }

}
