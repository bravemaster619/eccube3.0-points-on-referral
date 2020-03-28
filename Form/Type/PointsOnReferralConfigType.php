<?php


namespace Plugin\PointsOnReferral\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PointsOnReferralConfigType extends AbstractType {

    /**
     * @var \Eccube\Application
     */
    protected $app;

    /**
     * PointsOnReferralConfigType constructor.
     * * @param \Eccube\Application $app
     */
    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Build config type form
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('referrer_rewards_enabled', 'checkbox', array(
            'required' => false,
            'label' => $this->app['translator']->trans('admin.form.rewards.referrer.enabled.label'),
            'mapped' => true,
        ))->add('referrer_rewards', 'integer', array(
            'required' => true,
            'label' => $this->app['translator']->trans('admin.form.rewards.referrer.point.label'),
            'mapped' => true,
            'attr' => array(
                'placeholder' => $this->app['translator']->trans('admin.form.rewards.referrer.point.placeholder')
            ),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\GreaterThanOrEqual(0)
            )
        ))->add('referee_rewards_enabled', 'checkbox', array(
            'required' => false,
            'label' => $this->app['translator']->trans('admin.form.rewards.referee.enabled.label'),
            'mapped' => true
        ))->add('referee_rewards', 'integer', array(
            'required' => true,
            'label' => $this->app['translator']->trans('admin.form.rewards.referee.point.label'),
            'mapped' => true,
            'attr' => array(
                'placeholder' => $this->app['translator']->trans('admin.form.rewards.referee.point.placeholder')
            ),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\GreaterThanOrEqual(0)
            )
        ));
    }

    public function getName() {
        return 'admin_points_on_referral_config';
    }
}
