<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => "Vous devez remplir le champs"
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email([
                        'message' => "L'adresse {{ value }} N'est pas valide"

                    ]),
                    new NotBlank([
                        'message' => "Merci d'entrer une adresse mail"
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [

                'type' => PasswordType::class,
                'invalid_message' => "Les mot de passe doivent être identique",

                'first_options' => [
                    'label' => 'Mot de passe'
                ],

                'second_options' => [
                    'label' => 'Confirmation'
                ],


                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Trop court',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[ !\"\#\$%&\'\(\)*+,\-.\/:;<=>?@[\\^\]_`\{|\}~])^.{8,4096}$/',
                        'message' => 'le mot de passe et trop ez a brutForce',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            // TODO: Vire cette merde pour la prod wouhalla
            'attr' => [
                'novalidate' => 'novalidate'
            ]
        ]);
    }
}
