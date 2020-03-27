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
        $Customers = array();
        $count = 10;
        for($i = 0; $i < $count; $i++) {
            $Customers[] = $this->createCustomer();
        }
        $this->app['eccube.plugin.pointsonreferral.repository.customer']->updateAll($this->app);
        $this->assertEquals($count, count($this->app['eccube.plugin.pointsonreferral.repository.customer']->findAll()));

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
        // check customers are deleted
        $this->assertEquals($count - $deleteCount, count($this->app['eccube.repository.customer']->findAll()));
        $this->app['eccube.plugin.pointsonreferral.repository.customer']->updateAll($this->app);
        // check if customers count and PoRcustomer count are equal
        $this->assertEquals($count - $deleteCount, count($this->app['eccube.plugin.pointsonreferral.repository.customer']->findAll()));

        // check if customers count and PoRcustomer count are equal after insertion of one customer
        $Customer = $this->createCustomer();
        $this->app['eccube.plugin.pointsonreferral.repository.customer']->updateAll($this->app);
        $this->assertEquals($count - $deleteCount + 1, count($this->app['eccube.plugin.pointsonreferral.repository.customer']->findAll()));
    }

    public function createCustomer($email = null)
    {
        $faker = $this->getFaker();
        $Customer = new Customer();
        if (is_null($email)) {
            $email = $faker->email;
        }
        $Status = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::ACTIVE);
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setEmail($email)
            ->setPref($Pref)
            ->setPassword('password')
            ->setSecretKey($this->app['eccube.repository.customer']->getUniqueSecretKey($this->app))
            ->setStatus($Status)
            ->setDelFlg(0);
        $Customer->setPassword($this->app['eccube.repository.customer']->encryptPassword($this->app, $Customer));
        $this->app['orm.em']->persist($Customer);
        $this->app['orm.em']->flush();

        $CustomerAddress = new CustomerAddress();
        $CustomerAddress
            ->setCustomer($Customer)
            ->setDelFlg(0);
        $CustomerAddress->copyProperties($Customer);
        $this->app['orm.em']->persist($CustomerAddress);
        $this->app['orm.em']->flush();

        return $Customer;
    }

}
