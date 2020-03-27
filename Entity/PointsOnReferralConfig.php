<?php

namespace Plugin\PointsOnReferral\Entity;

/**
 * Class PointsOnReferralConfig
 * @package Plugin\PointsOnReferral\Entity
 */
class PointsOnReferralConfig extends \Eccube\Entity\AbstractEntity {

    /**
     * @var integer
     */
    private $id;

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
    private $referrer_rewards_enabled;

    /**
     * @var integer
     */
    private $referee_rewards_enabled;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return boolean
     */
    public function getReferrerRewardsEnabled() {
        return (bool)$this->referrer_rewards_enabled;
    }

    /**
     * @return integer
     */
    public function getReferrerRewards() {
        return $this->referrer_rewards;
    }

    /**
     * @return boolean
     */
    public function getRefereeRewardsEnabled() {
        return (bool)$this->referee_rewards_enabled;
    }

    /**
     * @return integer
     */
    public function getRefereeRewards() {
        return $this->referee_rewards;
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
     * @param $enabled boolean
     * @return PointsOnReferralConfig
     */
    public function setReferrerRewardsEnabled($enabled) {
        $this->referrer_rewards_enabled = $enabled ? 1 : 0;

        return $this;
    }

    /**
     * @param $point integer
     * @return PointsOnReferralConfig
     */
    public function setReferrerRewards($point) {
        $this->referrer_rewards = $point;

        return $this;
    }

    /**
     * @param $enabled boolean
     * @return PointsOnReferralConfig
     */
    public function setRefereeRewardsEnabled($enabled) {
        $this->referee_rewards_enabled = $enabled ? 1 : 0;

        return $this;
    }

    /**
     * @param $point integer
     * @return PointsOnReferralConfig
     */
    public function setRefereeRewards($point) {
        $this->referee_rewards = $point;

        return $this;
    }

    /**
     * @param $create_date \DateTime
     * @return PointsOnReferralConfig
     */
    public function setCreateDate(\DateTime $create_date) {
        $this->create_date = $create_date;

        return $this;
    }

    /**
     * @param $update_date \DateTime
     * @return PointsOnReferralConfig
     */
    public function setUpdateDate(\DateTime $update_date) {
        $this->update_date = $update_date;

        return $this;
    }

}
