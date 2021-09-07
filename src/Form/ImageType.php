<?php

namespace App\Form;

use App\Entity\Image as EntityImage;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre de l\'image',
                    'attr' => [
                        'placeholder' => 'Intitulé de l\'image',
                        'maxlength' => 70
                    ]
                ]
            )
            ->add(
                'file_name',
                FileType::class,
                [
                    'label' => 'Fichier de l\'image',
                    'mapped' => \false,
                    'constraints' => [
                        new Image([
                            'mimeTypes' => [
                                'image/jpg',
                                'image/jpeg',
                                'image/png'
                            ],
                            'mimeTypesMessage' => 'Veuillez sélectionner une image au format ".jpeg",".jpg" ou ".png", merci !'
                        ])
                    ],
                    'attr' => [
                        'accept' => "image/png, image/jpeg"
                    ]
                ]
            )
            ->add('imageTarget', HiddenType::class);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $formEvent) {
            /** @var EntityImage */
            $image = $formEvent->getData();
            // Limit the number of characters in the title
            if (\strlen($image->getTitle()) > 70) {
                $image->setTitle(\substr($image->getTitle(), 0, 70));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EntityImage::class
        ]);
    }
}
