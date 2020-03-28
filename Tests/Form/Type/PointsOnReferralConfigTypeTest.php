<?php


namespace Eccube\Tests\Form\Type\Admin;


use Eccube\Common\Constant;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class PointsOnReferralConfigTypeTest extends AbstractTypeTestCase {
    /**
     * @var \Eccube\Application
     */
    protected $app;

    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    public function setUp() {
        parent::setUp();
        $this->form = $this->app['form.factory']->createBuilder('admin_points_on_referral_config', null, array(
                'csrf_protection' => false
            ))->getForm();
    }

    public function testValidData() {
        $this->form->submit(array(
            'referrer_rewards_enabled' => Constant::ENABLED,
            'referrer_rewards' => 2000,
            'referee_rewards_enabled' => Constant::DISABLED,
            'referee_rewards' => 500
        ));
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidData() {
        $this->form->submit(array(
            'referrer_rewards_enabled' => Constant::ENABLED,
            'referrer_rewards' => 2000,
            'referee_rewards_enabled' => Constant::ENABLED,
            'referee_rewards' => -500
        ));
        $this->assertFalse($this->form->isValid());
    }

}
