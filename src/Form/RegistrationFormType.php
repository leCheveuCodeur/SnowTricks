<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pseudo', TextType::class, [
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'label'=> 'Pseudo',
                'attr'=>[
                    'placeholder'=>'Ecris ICI ton pseudo'
                ]
            ])
            ->add('email', EmailType::class, [
                'row_attr' => [
                    'class' => 'form-floating mb-3',
                ],
                'label'=> 'Email',
                'attr'=>[
                    'placeholder'=>'Ecris ICI ton email'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => 'Ecris ICI ton mot de passe'
                    ],
                    'label' => 'Mot de passe',
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Rentres un mot de passe ',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Ton mot de passe doit comporter au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'second_options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => 'Répètes ICI le mot de passe'
                    ],
                    'row_attr' => [
                        'class' => 'form-floating mb-3',
                    ],
                    'label' => 'Répètes le mot de passe',
                ],
                'invalid_message' => 'Les mots de passe doivent correspondre',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Les conditions générales doivent être acceptées',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
