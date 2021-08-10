<?php

namespace App\Form;

use App\Entity\Image as EntityImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'attr' => [
                        'placeholder' => 'Intitulé de l\'image'
                    ]
                ]
            )
            ->add(
                'path',
                FileType::class,
                [
                    'label' => 'Image',
                    'mapped' => \false,
                    'constraints' => [
                        new Image([
                            'mimeTypes' => [
                                'image/jpg',
                                'image/jpeg',
                                'image/png'
                            ],
                            'mimeTypesMessage' => 'Ce fichier n\'est pas une image valide.'
                        ])
                    ]
                ]
            );

        // ->add(
        //     'image',
        //     FileType::class,
        //     [
        //         'label' => 'Ajouter des images',
        //         'multiple' => \true,
        //         'mapped' => \false,
        //         'required' => \false,
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
        //             ]),
        //             'help' => 'Photo uniquement au format .jpeg /.jpg /.png'
        //         ]
        //     ]
        // );

        // ->add(
        //             'image',
        //             FileType::class,
        //             [
        //                 'label' => 'Ajouter une image',
        //                 'mapped' => \false,
        //                 'required' => \false,
        //                 'constraints' => [
        //                     new Image(
        //                         [
        //                             'mimeTypes' => [
        //                                 'image/jpg',
        //                                 'image/jpeg',
        //                                 'image/png'
        //                             ],
        //                             'mimeTypesMessage' => 'Ce fichier n\'est pas une image valide.'
        //                         ]
        //                     ),
        //                     'help' => 'Photo uniquement au format .jpeg /.jpg /.png'
        //                 ]
        //             ]
        //         );

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EntityImage::class,
        ]);
    }
}
