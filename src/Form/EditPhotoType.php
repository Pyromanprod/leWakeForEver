<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photo', FileType::class, [
                'label' => 'Selectionner une photo',
                'attr' => [
                    'accept' => 'image/jpeg, image/png'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de séléctionner un fichier',
                    ]),
                    new File([
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'tro gros ({{ size }} {{ sufix }}), taille maximum {{ limit }} {{ sufix }}',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Image non autorisé',
                    ])
                ]
            ])
        ->add('save', SubmitType::class,[
            'label'=>'Modifier',
            'attr'=>[
                'class'=>'btn btn-secondary w-100'
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
