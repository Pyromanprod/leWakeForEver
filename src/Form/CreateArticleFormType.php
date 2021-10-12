<?php

namespace App\Form;

use App\Entity\Article;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateArticleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('content',CKEditorType::class, [
                'label' => 'Titre de l\'article',
                'attr' => [
                    'class' => 'd-none'
                ],
                'purify_html'=>true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de remplir le contenu'
                    ]),
                    new Length([
                        'min' => 10,
                        'max' => 100000
                    ]),


                ]
            ])
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            // TODO: Vire cette merde pour la prod wouhalla
            'attr' => [
                'novalidate' => 'novalidate'
            ],
        ]);
    }
}
