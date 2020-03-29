<?php

namespace Plugin\PointsOnReferral\Repository;
use Doctrine\ORM\EntityRepository;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Plugin\PointsOnReferral\Entity\PointsOnReferralConfig;
use Plugin\PointsOnReferral\Entity\PointsOnReferralHistory;

class PointsOnReferralHistoryRepository extends EntityRepository{

    /**
     * @param Customer $Referrer
     * @param Customer $Referee
     * @param PointsOnReferralConfig $PoRConfig
     * @return PointsOnReferralHistory
     */
    public function create(Customer $Referrer, Customer $Referee, $PoRConfig) {
        $PoRHistory = new PointsOnReferralHistory();
        $PoRHistory->setReferrerId($Referrer->getId())
            ->setReferrerEmail($Referrer->getEmail())
            ->setReferrerName01($Referrer->getName01())
            ->setReferrerName02($Referrer->getName02())
            ->setReferrerKana01($Referrer->getKana01())
            ->setReferrerKana02($Referrer->getKana02())
            ->setRefereeId($Referee->getId())
            ->setRefereeEmail($Referee->getEmail())
            ->setRefereeName01($Referee->getName01())
            ->setRefereeName02($Referee->getName02())
            ->setRefereeKana01($Referee->getKana01())
            ->setRefereeKana02($Referee->getKana02())
            ->setReferrerReadDate(null)
            ->setRefereeReadDate(null)
            ->setReferrerShow(true)
            ->setRefereeShow(true)
            ->setReferrerRewards(0)
            ->setRefereeRewards(0)
            ->setCreateDate(date_create())
            ->setDelFlg(Constant::DISABLED);
        if ($PoRConfig->getReferrerRewardsEnabled() && $PoRConfig->getReferrerRewards() > 0) {
            $PoRHistory->setReferrerRewards($PoRConfig->getReferrerRewards());
        }
        if ($PoRConfig->getRefereeRewardsEnabled() && $PoRConfig->getRefereeRewards() > 0) {
            $PoRHistory->setRefereeRewards($PoRConfig->getRefereeRewards());
        }
        return $PoRHistory;
    }

    public function findReferralsByCustomer(Customer $Referrer) {
        $qb = $this->createQueryBuilder('h');
        $qb->where('h.referrer_id = :referrer_id')
            ->andWhere('h.referrer_show = :show')
            ->setParameters(array(
                'referrer_id' => $Referrer->getId(),
                'show' => Constant::ENABLED
            ))
            ->orderBy('h.plg_pointsonreferral_history_id', 'DESC')
            ->orderBy('h.create_date', 'DESC')
            ->select();
        return $qb->getQuery()->execute();
    }

    public function getQueryBuilderByCustomer(Customer $Customer, $ownership = PointsOnReferralHistory::REFERRER) {
        $qb = $this->createQueryBuilder('h');
        if ($ownership === PointsOnReferralHistory::REFERRER) {
            $qb->where('h.referrer_id = :customer_id');
            $qb->andWhere('h.referrer_show = :show');
        } else if ($ownership === PointsOnReferralHistory::REFEREE) {
            $qb->where('h.referee_id = :customer_id');
            $qb->andWhere('h.referee_show = :show');
        }
        $qb->setParameter('customer_id', $Customer->getId());
        $qb->setParameter('show', Constant::ENABLED);
        $qb->addOrderBy('h.plg_pointsonreferral_history_id', 'DESC');

        return $qb;
    }

}
