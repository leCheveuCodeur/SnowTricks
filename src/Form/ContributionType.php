<?php

namespace App\Form;

use App\Entity\Trick;
use App\Form\ImageType;
use App\Entity\Category;
use App\Entity\Contribution;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContributionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //  To know if it's a contribution on an existing Trick or not
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {
            $builder = $formEvent->getForm();

            /** @var Contribution */
            $contribution = $formEvent->getData();

            // See if contribution contains the data of one Trick
            /** @var Trick */
            $isExistantTrick = $contribution->getTrick() ?? \false;

            if (!$isExistantTrick) {
                $builder
                    ->add(
                        'title',
                        TextType::class,
                        [
                            'label' => 'Nom de la figure',
                            'attr' => [
                                'placeholder' => 'Tapez ici le nom'
                            ]
                        ]
                    )
                    ->add(
                        'category',
                        EntityType::class,
                        [
                            'label' => 'Category',
                            'attr' => ['class' => 'form-control'],
                            'placeholder' => '-- Choisir une catégorie --',
                            'class' => Category::class,
                            'choice_label' => function (Category $category) {
                                return strtoupper($category->getName());
                            }
                        ]
                    );
            }


            $builder
                ->add(
                    'lead_in',
                    TextareaType::class,
                    [
                        'label' => 'Intro courte de la figure',
                        'data' => $isExistantTrick ? $isExistantTrick->getLeadIn() : '',
                        'attr' => [
                            'placeholder' => $isExistantTrick ? '' : 'Tapez ici l\'intro'
                        ]
                    ]
                )
                ->add(
                    'content',
                    TextareaType::class,
                    [
                        'label' => 'Description de la figure',
                        'data' => $isExistantTrick ? $isExistantTrick->getContent() : '',
                        'attr' => [
                            'placeholder' => $isExistantTrick ? '' : 'Tapez ici la description'
                        ]
                    ]
                )
                ->add(
                    'images',
                    CollectionType::class,
                    [
                        'entry_type' => ImageType::class,
                        'entry_options' => [
                            'label' => false
                        ],
                        'label' => false,
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'required' => false
                    ]
                );
            // ->add(
            //     'images',
            //     FileType::class,
            //     [
            //         'label' => 'Ajouter des images',
            //         'multiple' => \true,
            //         'mapped' => \false,
            //         'required' => \false,
            //         'attr' => [
            //             'onchange' => 'previewImage($this)'
            //         ],
            //         'constraints' => [
            //             new All([
            //                 'constraints' => [
            //                     new Image([
            //                         'mimeTypes' => [
            //                             'image/jpg',
            //                             'image/jpeg',
            //                             'image/png'
            //                         ],
            //                         'mimeTypesMessage' => 'Ce fichier n\'est pas une image valide.'
            //                     ])
            //                 ]
            //             ])
            //         ]
            //     ]
            // );
        });
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contribution::class,
        ]);
    }
}