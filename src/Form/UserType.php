<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
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
            ->add(
                'loyaltyRewards',
                null,
                [
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
    }
}
