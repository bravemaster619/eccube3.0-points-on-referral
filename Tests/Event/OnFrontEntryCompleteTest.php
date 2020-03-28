<?php

namespace Eccube\Tests\Event;

use Eccube\Tests\EccubeTestCase;

class OnFrontEntryCompleteTest extends EccubeTestCase {

    public function test() {
        $Referrer = $this->createCustomer();
        $PoRReferrer = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($this->app, $Referrer);
        $this->app['orm.em']->persist($PoRReferrer);
        $this->app['orm.em']->flush();
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
        $this->assertEmpty($this->app['session']->get($session_key), "Referral code should be removed from the session after signup");

        $PoRReferee = $this->app['eccube.plugin.pointsonreferral.repository.customer']->findOrCreateByCustomer($this->app, $Referee);
        $this->assertNotEmpty($PoRReferee->getPointsOnReferralCustomerId(), "PoRCustomer should be created when a customer is signed up");
        $this->expected = $Referrer->getId();
        $this->actual = $PoRReferee->getReferrerId();
        $this->verify("New customer should be referred by the one created earlier");
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
