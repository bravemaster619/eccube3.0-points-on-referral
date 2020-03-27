<?php

namespace Plugin\PointsOnReferral\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Eccube\Entity\Customer;
use Plugin\PointsOnReferral\Entity\PointsOnReferralCustomer;
use Plugin\PointsOnReferral\Helper\PointsOnReferralHelper;

class PointsOnReferralCustomerRepository extends EntityRepository {

    protected $app;

    public function setApplication($app) {
        $this->app = $app;
    }

    /**
     * @param $app
     * @param Customer $Customer
     * @return PointsOnReferralCustomer
     */
    public function createFromCustomer($app, Customer $Customer) {
        $PoRCustomer = new PointsOnReferralCustomer();
        $PoRHelper = new PointsOnReferralHelper($app);
        $PoRCustomer->setCustomer($Customer)
            ->setCustomerId($Customer->getId())
            ->setReferralCode($PoRHelper->generateReferralCode())
            ->setCreateDate(date_create())
            ->setUpdateDate(date_create());
        return $PoRCustomer;
    }

    /**
     * @param $app
     * @param $Customer
     */
    public function findOrCreateByCustomer($app, $Customer) {
        $CustomerRepository = $app['eccube.repository.customer'];
        if (!is_object($Customer)) {
            $Customer = $CustomerRepository->find($Customer);
        }
        $PoRCustomer = $this->findOneBy(array(
           'customer_id' => $Customer->getId()
        ));
        if ($PoRCustomer) {
            return $PoRCustomer;
        } else {
            return $this->createFromCustomer($app, $Customer);
        }
    }

    /**
     * @param $referral_code string
     * @return PointsOnReferralCustomer|null
     */
    public function findOneByReferralCode($referral_code) {
        try {
            $qb = $this->createQueryBuilder('p');
            $qb->where('p.referral_code = :referral_code')
                ->setParameter('referral_code', $referral_code)
                ->setMaxResults(1);
            // NonUniqueResultException ignored because referral_code is in unique key constraint
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * This method will update the PoRCustomer table from Customer table.
     * <p>This method will do the following tasks:</p>
     * <ul>
     *   <li> Any customer missing in Customer table will be also deleted from PoRCustomer table </li>
     *   <li> Any customer missing in PoRCustomer table will be added with referral codes </li>
     *   <li> Any PoRCustomer missing a referral code will be assigned a new referral code </li>
     * </ul>
     * @param $app
     */
    public function updateAll($app) {
        $ActiveCustomers = $app['eccube.repository.customer']->findAll();
        $PoRHelper = new PointsOnReferralHelper($app);
        $processedCustomerIds = array();
        foreach($ActiveCustomers as $Customer) {
            $PoRCustomer = $this->findOrCreateByCustomer($app, $Customer);
            if (!$PoRCustomer->getReferralCode()) {
                $PoRCustomer->setReferralCode($PoRHelper->generateReferralCode());
                $PoRCustomer->setUpdateDate(date_create());
                $app['orm.em']->persist($PoRCustomer);
            }
            if (!$PoRCustomer->getPointsOnReferralCustomerId()) {
                $app['orm.em']->persist($PoRCustomer);
            }
            $processedCustomerIds[] = $Customer->getId();
        }
        $qb = $this->createQueryBuilder('p');
        $query = $qb->where('p.customer_id NOT IN (:processed_ids)')
            ->setParameter('processed_ids', $processedCustomerIds, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->delete()
            ->getQuery();
        $query->execute();
        $app['orm.em']->flush();
    }

}
