<?php

namespace Eccube\Tests\Repository;

use Eccube\Tests\EccubeTestCase;

class PointsOnReferralConfigRepositoryTest extends EccubeTestCase {

    public function testGetConfig() {
        $Config = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $this->assertNotNull($Config);
    }

}
