<?php

namespace Plugin\PointsOnReferral\Entity;

use Eccube\Entity\Customer;

class PointsOnReferralHistory extends \Eccube\Entity\AbstractEntity {

    const REFERRER = 1;
    const REFEREE = 2;
    const UNKNOWN = 0;

    /**
     * @var integer
     */
    private $pointsonreferral_history_id;

    /**
     * @var integer
     */
    private $referrer_id;

    /**
     * @var string
     */
    private $referrer_email;

    /**
     * @var string
     */
    private $referrer_name01;

    /**
     * @var string
     */
    private $referrer_name02;

    /**
     * @var string
     */
    private $referrer_kana01;

    /**
     * @var string
     */
    private $referrer_kana02;

    /**
     * @var integer
     */
    private $referee_id;

    /**
     * @var string
     */
    private $referee_email;

    /**
     * @var string
     */
    private $referee_name01;

    /**
     * @var string
     */
    private $referee_name02;

    /**
     * @var string
     */
    private $referee_kana01;

    /**
     * @var string
     */
    private $referee_kana02;

    /**
     * @var integer
     */
    private $referrer_rewards;

    /**
     * @var integer
     */
    private $referee_rewards;

    /**
     * @var integer
     */
    private $referrer_show;

    /**
     * @var integer
     */
    private $referee_show;

    /**
     * @var \DateTime|null
     */
    private $referrer_read_date;

    /**
     * @var \DateTime|null
     */
    private $referee_read_date;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @return integer
     */
    public function getId() {
        return $this->pointsonreferral_history_id;
    }

    /**
     * @return integer
     */
    public function getReferrerId() {
        return $this->referrer_id;
    }

    /**
     * @return string
     */
    public function getReferrerEmail() {
        return $this->referrer_email;
    }

    /**
     * @return string
     */
    public function getReferrerName01() {
        return $this->referrer_name01;
    }

    /**
     * @return string
     */
    public function getReferrerName02() {
        return $this->referrer_name02;
    }

    /**
     * @return string
     */
    public function getReferrerKana01() {
        return $this->referrer_kana01;
    }

    /**
     * @return string
     */
    public function getReferrerKana02() {
        return $this->referrer_kana02;
    }

    /**
     * @return integer
     */
    public function getRefereeId() {
        return $this->referee_id;
    }

    /**
     * @return string
     */
    public function getRefereeEmail() {
        return $this->referee_email;
    }

    /**
     * @return string
     */
    public function getRefereeName01() {
        return $this->referee_name01;
    }

    /**
     * @return string
     */
    public function getRefereeName02() {
        return $this->referee_name02;
    }

    /**
     * @return string
     */
    public function getRefereeKana01() {
        return $this->referee_kana01;
    }

    /**
     * @return string
     */
    public function getRefereeKana02() {
        return $this->referee_kana02;
    }

    /**
     * @return integer
     */
    public function getReferrerRewards() {
        return $this->referrer_rewards;
    }

    /**
     * @return integer
     */
    public function getRefereeRewards() {
        return $this->referee_rewards;
    }

    /**
     * @return boolean
     */
    public function getReferrerShow() {
        return (bool) $this->referrer_show;
    }

    /**
     * @return boolean
     */
    public function getRefereeShow() {
        return (bool) $this->referee_show;
    }

    /**
     * @return \DateTime|null
     */
    public function getReferrerReadDate() {
        return $this->referrer_read_date;
    }

    /**
     * @return \DateTime|null
     */
    public function getRefereeReadDate() {
        return $this->referee_read_date;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate() {
        return $this->create_date;
    }

    /**
     * @return integer
     */
    public function getDelFlg() {
        return $this->del_flg;
    }

    /**
     * @return string
     */
    public function getReferrerFullName() {
        return "$this->referrer_name01 $this->referrer_name02";
    }

    /**
     * @return string
     */
    public function getReferrerFullKana() {
        return "$this->referrer_kana01 $this->referrer_kana02";
    }

    /**
     * @return string
     */
    public function getRefereeFullName() {
        return "$this->referee_name01 $this->referee_name02";
    }

    /**
     * @return string
     */
    public function getRefereeFullKana() {
        return "$this->referee_kana01 $this->referee_kana02";
    }

    /**
     * @param $Customer integer|Customer
     * @return integer
     */
    public function getOwnerShip($Customer) {
        if ($Customer instanceof Customer) {
            $customer_id = $Customer->getId();
        } else {
            $customer_id = $Customer;
        }
        if ($customer_id == $this->referrer_id) {
            return self::REFERRER;
        } else if ($customer_id == $this->referee_id) {
            return self::REFEREE;
        } else {
            return self::UNKNOWN;
        }
    }

    /**
     * @param $Customer integer|Customer
     * @return boolean
     */
    public function hasRead($Customer) {
        $ownership = $this->getOwnerShip($Customer);
        switch ($ownership) {
            case self::REFERRER:
                return $this->referrer_read_date != null;
            case self::REFEREE:
                return $this->referee_read_date != null;
            default:
                return false;
        }
    }

    /**
     * @return PointsOnReferralHistory
     */
    public function setId() {
        return $this;
    }

    /**
     * @param $referrer_id integer
     * @return PointsOnReferralHistory
     */
    public function setReferrerId($referrer_id) {
        $this->referrer_id = $referrer_id;

        return $this;
    }

    /**
     * @param $referrer_email string
     * @return PointsOnReferralHistory
     */
    public function setReferrerEmail($referrer_email) {
        $this->referrer_email = $referrer_email;

        return $this;
    }

    /**
     * @param $name01 string
     * @return PointsOnReferralHistory
     */
    public function setReferrerName01($name01) {
        $this->referrer_name01 = $name01;

        return $this;
    }

    /**
     * @param $name02 string
     * @return PointsOnReferralHistory
     */
    public function setReferrerName02($name02) {
        $this->referrer_name02 = $name02;

        return $this;
    }

    /**
     * @param $kana01 string
     * @return PointsOnReferralHistory
     */
    public function setReferrerKana01($kana01) {
        $this->referrer_kana01 = $kana01;

        return $this;
    }

    /**
     * @param $kana02 string
     * @return PointsOnReferralHistory
     */
    public function setReferrerKana02($kana02) {
        $this->referrer_kana02 = $kana02;

        return $this;
    }

    /**
     * @param $referee_id integer
     * @return PointsOnReferralHistory
     */
    public function setRefereeId($referee_id) {
        $this->referee_id = $referee_id;

        return $this;
    }

    /**
     * @param $referee_email string
     * @return PointsOnReferralHistory
     */
    public function setRefereeEmail($referee_email) {
        $this->referee_email = $referee_email;

        return $this;
    }

    /**
     * @param $name01 string
     * @return PointsOnReferralHistory
     */
    public function setRefereeName01($name01) {
        $this->referee_name01 = $name01;

        return $this;
    }

    /**
     * @param $name02 string
     * @return PointsOnReferralHistory
     */
    public function setRefereeName02($name02) {
        $this->referee_name02 = $name02;

        return $this;
    }

    /**
     * @param $kana01 string
     * @return PointsOnReferralHistory
     */
    public function setRefereeKana01($kana01) {
        $this->referee_kana01 = $kana01;

        return $this;
    }

    /**
     * @param $kana02 string
     * @return PointsOnReferralHistory
     */
    public function setRefereeKana02($kana02) {
        $this->referee_kana02 = $kana02;

        return $this;
    }

    /**
     * @param $point integer
     * @return PointsOnReferralHistory
     */
    public function setReferrerRewards($point) {
        $this->referrer_rewards = $point;

        return $this;
    }

    /**
     * @param $point integer
     * @return PointsOnReferralHistory
     */
    public function setRefereeRewards($point) {
        $this->referee_rewards = $point;

        return $this;
    }

    /**
     * @param $show
     * @return PointsOnReferralHistory
     */
    public function setReferrerShow($show) {
        $this->referrer_show = $show ? 1 : 0;

        return $this;
    }

    /**
     * @param $show
     * @return PointsOnReferralHistory
     */
    public function setRefereeShow($show) {
        $this->referee_show = $show ? 1 : 0;

        return $this;
    }

    /**
     * @param $date \DateTime
     * @return PointsOnReferralHistory
     */
    public function setReferrerReadDate($date) {
        $this->referrer_read_date = $date;

        return $this;
    }

    /**
     * @param $date \DateTime
     * @return PointsOnReferralHistory
     */
    public function setRefereeReadDate($date) {
        $this->referee_read_date = $date;

        return $this;
    }

    /**
     * @param $date \DateTime
     * @return PointsOnReferralHistory
     */
    public function setCreateDate($date) {
        $this->create_date = $date;

        return $this;
    }

    /**
     * @param $del_flg
     * @return PointsOnReferralHistory
     */
    public function setDelFlg($del_flg) {
        $this->del_flg = $del_flg;

        return $this;
    }

    /**
     * @param $Customer
     * @return PointsOnReferralHistory
     */
    public function readBy($Customer) {
        $ownership = $this->getOwnerShip($Customer);
        switch($ownership) {
            case self::REFERRER:
                $this->setReferrerReadDate(date_create());
                break;
            case self::REFEREE:
                $this->setRefereeReadDate(date_create());
                break;
            default: break;
        }
        return $this;
    }

}
