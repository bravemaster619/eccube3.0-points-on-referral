<?php

namespace Eccube\Tests\Event;

use Eccube\Entity\Customer;
use Eccube\Tests\EccubeTestCase;
use Plugin\PointsOnReferral\Entity\PointsOnReferralCustomer;

class OnFrontEntryCompleteTest extends EccubeTestCase {

    public function testReferral() {
        $PoRReferrer = $this->createPoRReferrer();
        $Referrer = $PoRReferrer->getCustomer();
        $PoRReferee = $this->createPoRReferee($Referrer, $PoRReferrer);
        $this->assertNotEmpty($PoRReferee->getPointsOnReferralCustomerId(), "PoRCustomer should be created when a customer is signed up");
        $this->expected = $Referrer->getId();
        $this->actual = $PoRReferee->getReferrerId();
        $this->verify("New customer should be referred by the one created earlier");
    }

    public function testReferrerRewards() {
        $PoRConfig = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $PoRConfig->setReferrerRewardsEnabled(true)
            ->setReferrerRewards(2500)
            ->setRefereeRewardsEnabled(false);
        $this->app['eccube.plugin.pointsonreferral.repository.config']->save($PoRConfig);
        $PoRReferrer = $this->createPoRReferrer();
        $Referrer = $PoRReferrer->getCustomer();
        $PoRReferee = $this->createPoRReferee($Referrer, $PoRReferrer);
        $Referee = $PoRReferee->getCustomer();
        $this->activateCustomer($Referee);
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->findOneBy(array(), array('plg_pointsonreferral_history_id' => 'DESC'));
        $this->assertEquals($PoRConfig->getReferrerRewards(), $PoRHistory->getReferrerRewards(), "Referrer should receive rewards");
        $this->assertEquals(0, $PoRHistory->getRefereeRewards(), "Referee should not receive rewards");
    }

    public function testRefereeRewards() {
        $PoRConfig = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $PoRConfig->setReferrerRewardsEnabled(false)
            ->setReferrerRewards(2500)
            ->setRefereeRewardsEnabled(true)
            ->setRefereeRewards(3500);
        $this->app['eccube.plugin.pointsonreferral.repository.config']->save($PoRConfig);
        $PoRReferrer = $this->createPoRReferrer();
        $Referrer = $PoRReferrer->getCustomer();
        $PoRReferee = $this->createPoRReferee($Referrer, $PoRReferrer);
        $Referee = $PoRReferee->getCustomer();
        $this->activateCustomer($Referee);
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->findOneBy(array(), array('plg_pointsonreferral_history_id' => 'DESC'));
        $this->assertEquals(0, $PoRHistory->getReferrerRewards(), "Referrer should not receive rewards");
        $this->assertEquals($PoRConfig->getRefereeRewards(), $PoRHistory->getRefereeRewards(), "Referee should receive rewards");
    }

    public function testBothRewards() {
        $PoRConfig = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $PoRConfig->setReferrerRewardsEnabled(true)
            ->setReferrerRewards(4200)
            ->setRefereeRewardsEnabled(true)
            ->setRefereeRewards(3600);
        $this->app['eccube.plugin.pointsonreferral.repository.config']->save($PoRConfig);
        $PoRReferrer = $this->createPoRReferrer();
        $Referrer = $PoRReferrer->getCustomer();
        $PoRReferee = $this->createPoRReferee($Referrer, $PoRReferrer);
        $Referee = $PoRReferee->getCustomer();
        $this->activateCustomer($Referee);
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->findOneBy(array(), array('plg_pointsonreferral_history_id' => 'DESC'));
        $this->assertEquals($PoRConfig->getReferrerRewards(), $PoRHistory->getReferrerRewards(), "Referrer should receive rewards");
        $this->assertEquals($PoRConfig->getRefereeRewards(), $PoRHistory->getRefereeRewards(), "Referee should receive rewards");
    }

    public function testDisabledRewards() {
        $PoRConfig = $this->app['eccube.plugin.pointsonreferral.repository.config']->getConfig();
        $PoRConfig->setReferrerRewardsEnabled(false)
            ->setReferrerRewards(1400)
            ->setRefereeRewardsEnabled(false)
            ->setRefereeRewards(700);
        $this->app['eccube.plugin.pointsonreferral.repository.config']->save($PoRConfig);
        $PoRReferrer = $this->createPoRReferrer();
        $Referrer = $PoRReferrer->getCustomer();
        $PoRReferee = $this->createPoRReferee($Referrer, $PoRReferrer);
        $Referee = $PoRReferee->getCustomer();
        $this->activateCustomer($Referee);
        $PoRHistory = $this->app['eccube.plugin.pointsonreferral.repository.history']->findOneBy(array(), array('plg_pointsonreferral_history_id' => 'DESC'));
        $this->assertEquals(0, $PoRHistory->getReferrerRewards(), "Referrer should not receive rewards");
        $this->assertEquals(0, $PoRHistory->getRefereeRewards(), "Referee should not receive rewards");
    }

    /**
     * @return PointsOnReferralCustomer
     */
    public function createPoRReferrer() {
        $Referrer = $this->createCustomer();
        $PoRReferrer = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($this->app, $Referrer);
        $this->app['orm.em']->persist($PoRReferrer);
        $this->app['orm.em']->flush();
        return $PoRReferrer;
    }
    /**
     * @param $Referrer Customer
     * @param $PoRReferrer PointsOnReferralCustomer
     * @return PointsOnReferralCustomer
     */
    public function createPoRReferee(Customer $Referrer, PointsOnReferralCustomer $PoRReferrer) {
        $query_key = $this->app['config']['PointsOnReferral']['const']['referral_code_query_key'];
        $session_key = $this->app['config']['PointsOnReferral']['const']['session_key'];
        $crawler = $this->createClient()->request(
            'GET',
            $this->app->path('entry') . "?" . $query_key . "=" . $PoRReferrer->getReferralCode()
        );
        $userData = $this->createSignupFormData();
        $crawler = $this->createClient()->request(
            'POST',
            $this->app['url_generator']->generate('entry'),
            array(
                'mode' => 'complete',
                'entry' => $userData
            )
        );
        $Referee = $this->app['eccube.repository.customer']->findOneBy(array(
            'email' => $userData['email']['first']
        ));
        $PoRReferee = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($this->app, $Referee);
        return $PoRReferee;
    }

    public function activateCustomer(Customer $Customer) {
        $crawler = $this->createClient()->request(
            'GET',
            $this->app->path('entry_activate', array(
                'secret_key' => $Customer->getSecretKey()
            ))
        );
    }

    public function createSignupFormData() {
        $faker = $this->getFaker();
        $tel = explode('-', $faker->phoneNumber);

        $email = $faker->safeEmail;
        $password = $faker->lexify('????????');
        $birth = $faker->dateTimeBetween;

        $form = array(
            'name' => array(
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ),
            'kana' => array(
                'kana01' => $faker->lastKanaName ,
                'kana02' => $faker->firstKanaName,
            ),
            'company_name' => $faker->company,
            'zip' => array(
                'zip01' => $faker->postcode1(),
                'zip02' => $faker->postcode2(),
            ),
            'address' => array(
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ),
            'tel' => array(
                'tel01' => $tel[0],
                'tel02' => $tel[1],
                'tel03' => $tel[2],
            ),
            'fax' => array(
                'fax01' => $tel[0],
                'fax02' => $tel[1],
                'fax03' => $tel[2],
            ),
            'email' => array(
                'first' => $email,
                'second' => $email,
            ),
            'password' => array(
                'first' => $password,
                'second' => $password,
            ),
            'birth' => array(
                'year' => $birth->format('Y'),
                'month' => $birth->format('n'),
                'day' => $birth->format('j'),
            ),
            'sex' => 1,
            'job' => 1,
            '_token' => 'dummy'
        );
        return $form;
    }

}
