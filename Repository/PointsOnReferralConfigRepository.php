<?php

namespace Plugin\PointsOnReferral\Repository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Plugin\PointsOnReferral\Entity\PointsOnReferralConfig;

class PointsOnReferralConfigRepository extends EntityRepository {

    /**
     * @return mixed|null
     */
    public function getConfig() {
        try {
            return $this->createQueryBuilder("p")
                ->orderBy("p.id", "DESC")
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * @return PointsOnReferralConfig
     */
    public function seed() {
        $Config = new PointsOnReferralConfig();
        $Config->setReferrerRewards(0)
            ->setRefereeRewards(0)
            ->setReferrerRewards(true)
            ->setRefereeRewardsEnabled(true)
            ->setCreateDate(date_create())
            ->setUpdateDate(date_create());
        $this->save($Config);
        return $Config;
    }

    /**
     * @param PointsOnReferralConfig $Config
     * @return PointsOnReferralConfigRepository
     */
    public function save(PointsOnReferralConfig $Config) {
        $this->getEntityManager()->persist($Config);
        $this->getEntityManager()->flush();
        return $this;
    }

}
