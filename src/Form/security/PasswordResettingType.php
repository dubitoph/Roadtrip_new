<?php

namespace App\Form\security;

use App\Entity\user\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class PasswordResettingType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('password', RepeatedType::class, [
                                                        'type' => PasswordType::class,
                                                        'invalid_message' => 'The password fields must match.',
                                                        'options' => ['attr' => ['class' => 'password-field']],
                                                        'required' => true,
                                                        'first_options'  => ['label' => 'Password'],
                                                        'second_options' => ['label' => 'Repeat your password']
                                                       ]
                     )

        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(
                                [
                                    'data_class' => User::class,
                                    'translation_domain' => 'forms'
                                ]
                              )
        ;

    }

}