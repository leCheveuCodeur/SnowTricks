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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ImageType extends AbstractType
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Request */
        $request = $this->requestStack->getCurrentRequest();

        // index in the iterative builder
        $index = intval($builder->getName());
        /** @var UploadedFile */
        $hasFile = $request->files->get('contribution')['images'][$index - 1]['file_name'] ?? false;

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
                            'mimeTypesMessage' => "Format d'image accepté: .jpeg / .jpg / .png"
                        ])
                    ],
                    'attr' => [
                        'accept' => "image/png, image/jpeg",
                        'title' => "Format d'image accepté: .jpeg / .jpg / .png"
                    ]
                ]
            )
            ->add('originalFileName', HiddenType::class)
            ->add('imageTarget', HiddenType::class)
            ->add('in_front', HiddenType::class);



        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $formEvent) {
            $imageData = $formEvent->getData();
            // Limit the number of characters in the title
            if (\strlen($imageData['title']) > 70) {
                $imageData['title'] = \substr($imageData['title'], 0, 70);
            }
            if (!\key_exists('originalFileName', $imageData)) {
                /** @var UploadedFile */
                $file = $imageData['file_name'];
                $imageData['originalFileName'] = $file->getClientOriginalName();
            }
            $formEvent->setData($imageData);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EntityImage::class
        ]);
    }
}
