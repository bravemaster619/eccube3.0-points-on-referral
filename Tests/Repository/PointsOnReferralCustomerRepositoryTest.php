<?php

namespace Eccube\Tests\Repository;

use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Tests\EccubeTestCase;

class PointsOnReferralCustomerRepositoryTest extends EccubeTestCase {

    public function testCreateFromCustomer() {
        $Customer = $this->createCustomer();
        $PoRCustomer = $this->app['eccube.plugin.pointsonreferral.repository.customer']->createFromCustomer($this->app, $Customer);
        $this->assertNotNull($PoRCustomer);
        $this->assertEquals($PoRCustomer->getCustomerId(), $Customer->getId());
        $this->assertGreaterThan(16, strlen($PoRCustomer->getReferralCode()));
    }

    public function testFindOrCreateByCustomer() {
        $Customer = $this->createCustomer();
        $PoRCustomer = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($this->app, $Customer);
        $this->app['orm.em']->persist($PoRCustomer);
        $this->app['orm.em']->flush();
        $Persisted = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($this->app, $Customer);
        $this->assertEquals($PoRCustomer->getPointsOnReferralCustomerId(), $Persisted->getPointsOnReferralCustomerId());
        $this->assertEquals($PoRCustomer->getReferralCode(), $Persisted->getReferralCode());
    }

    public function testFindOneByReferralCode() {
        $Customer = $this->createCustomer();
        $PoRCustomer = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($this->app, $Customer);
        $this->app['orm.em']->persist($PoRCustomer);
        $this->app['orm.em']->flush();
        $Persisted = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOneByReferralCode($PoRCustomer->getReferralCode());
        $this->assertNotNull($Persisted);
        $this->assertEquals($PoRCustomer->getReferralCode(), $Persisted->getReferralCode());
        $this->assertEquals($PoRCustomer->getPointsOnReferralCustomerId(), $Persisted->getPointsOnReferralCustomerId());
        $this->assertNull($this->app['eccube.plugin.pointsonreferral.repository.customer']->findOneByReferralCode('************'));
    }

    public function testUpdateAllCustomers() {
        // check if customers count and PoRcustomer count are equal
        $this->app['eccube.plugin.pointsonreferral.repository.customer']->updateAll($this->app);
        $this->assertEquals(count($this->app['eccube.repository.customer']->findAll()), count($this->app['eccube.plugin.pointsonreferral.repository.customer']->findAll()));
        // check if customers count and PoRcustomer count are equal after insertion
        $Customers = array();
        $count = 10;
        for($i = 0; $i < $count; $i++) {
            $Customers[] = $this->createCustomer();
        }
        $this->app['eccube.plugin.pointsonreferral.repository.customer']->updateAll($this->app);
        $this->assertEquals(count($this->app['eccube.repository.customer']->findAll()), count($this->app['eccube.plugin.pointsonreferral.repository.customer']->findAll()));

        // check if customers count and PoRcustomer count are equal after force deletion of customers
        $deleteCount = 6;
        // force delete customers without foreign key check
        $this->app['orm.em']->getConnection()->exec('SET FOREIGN_KEY_CHECKS = 0;');
        $qb = $this->app['orm.em']->createQueryBuilder();
        $query = $qb->delete('Eccube\Entity\Customer', 'c')
            ->where('c.id < :last_id')
            ->setParameter('last_id', $Customers[$deleteCount]->getId())
            ->getQuery();
        $query->execute();
        $this->app['eccube.plugin.pointsonreferral.repository.customer']->updateAll($this->app);
        $this->assertEquals(count($this->app['eccube.repository.customer']->findAll()), count($this->app['eccube.plugin.pointsonreferral.repository.customer']->findAll()));
    }
}
