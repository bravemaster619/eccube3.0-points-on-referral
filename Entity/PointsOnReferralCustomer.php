<?php

namespace Plugin\PointsOnReferral\Entity;

use Eccube\Entity\Customer;


/**
 * PointsOnReferralCustomer
 *
 *  @UniqueEntity("referral_code")
 */
class PointsOnReferralCustomer extends \Eccube\Entity\AbstractEntity {

    /**
     * @var integer
     */
    private $pointsonreferral_customer_id;

    /**
     * @var integer
     */
    private $customer_id;

    /**
     * Referrer customer id, it's not a plugin customer id
     * @var integer
     */
    private $referrer_id;

    /**
     * @var string
     */
    private $referral_code;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var Customer
     */
    private $Customer;

    /**
     * @var Customer
     */
    private $ReferrerCustomer;


    /**
     * @return integer
     */
    public function getPointsOnReferralCustomerId() {
        return $this->pointsonreferral_customer_id;
    }

    /**
     * @return integer
     */
    public function getCustomerId() {
        return $this->customer_id;
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
    public function getReferralCode() {
        return $this->referral_code;
    }

    /**
     * @return \DateTime
     */
    public function getCreateDate() {
        return $this->create_date;
    }

    /**
     * @return \DateTime
     */
    public function getUpdateDate() {
        return $this->update_date;
    }

    /**
     * @return Customer
     */
    public function getCustomer() {
        return $this->Customer;
    }

    /**
     * @return PointCustomer
     */
    public function getPointCustomer() {
        return $this->PointCustomer;
    }

    /**
     * @return Customer
     */
    public function getReferrerCustomer() {
        return $this->ReferrerCustomer;
    }

    /**
     * @return PointCustomer
     */
    public function getReferrerPointCustomer() {
        return $this->ReferrerPointCustomer;
    }

    /**
     * @param $customer_id integer
     * @return PointsOnReferralCustomer
     */
    public function setCustomerId($customer_id) {
        $this->customer_id = $customer_id;

        return $this;
    }

    /**
     * @param $referrer_id integer
     * @return PointsOnReferralCustomer
     */
    public function setReferrerId($referrer_id) {
        $this->referrer_id = $referrer_id;

        return $this;
    }

    /**
     * @param $referral_code string
     * @return PointsOnReferralCustomer
     */
    public function setReferralCode($referral_code) {
        $this->referral_code = $referral_code;

        return $this;
    }

    /**
     * @param $Customer Customer
     * @return PointsOnReferralCustomer
     */
    public function setCustomer(Customer $Customer) {
        $this->Customer = $Customer;

        return $this;
    }

    /**
     * @param $ReferrerCustomer Customer
     * @return PointsOnReferralCustomer
     */
    public function setReferrerCustomer(Customer $ReferrerCustomer) {
        $this->ReferrerCustomer = $ReferrerCustomer;

        return $this;
    }

    /**
     * @param $create_date \DateTime
     * @return PointsOnReferralCustomer
     */
    public function setCreateDate(\DateTime $create_date) {
        $this->create_date = $create_date;

        return $this;
    }

    /**
     * @param $update_date \DateTime
     * @return PointsOnReferralCustomer
     */
    public function setUpdateDate(\DateTime $update_date) {
        $this->update_date = $update_date;

        return $this;
    }
}
