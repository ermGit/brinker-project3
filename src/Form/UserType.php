<?php

namespace App\Form;

use App\Entity\LoyaltyReward;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * Creates the form for the User.
     * The Loyalty Rewards section is tied to the rewards present in the db table
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add(
                'roles',
                HiddenType::class,
                [
                    'data' => json_encode(['ROLE_GUEST']),
                    'mapped' => false,
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'mapped' => false, // You'll hash it in the controller
                    'required' => $options['is_edit'] === false,
                ]
            )
            ->add('loyaltyRewards', EntityType::class, [
                'class' => LoyaltyReward::class,
                'choice_label' => fn (LoyaltyReward $reward) => $reward->getRewardName(),
                'multiple' => true,
                'expanded' => true, // checkbox display; set to false for multi-select dropdown
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
