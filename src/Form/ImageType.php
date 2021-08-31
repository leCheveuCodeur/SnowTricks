<?php

namespace App\Form;

use App\Entity\Image as EntityImage;
use App\Service\FileUploaderHelper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageType extends AbstractType
{
    private $em;
    private $fileUploaderHelper;

    public function __construct(EntityManagerInterface $em, FileUploaderHelper $fileUploaderHelper)
    {
        $this->em = $em;
        $this->fileUploaderHelper = $fileUploaderHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre de l\'image',
                    'attr' => [
                        'placeholder' => 'Intitulé de l\'image'
                    ]
                ]
            )
            ->add(
                'path',
                FileType::class,
                [
                    'label' => 'Fichier de l\'image',
                    'mapped' => \false,
                    'required' => \true,
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
            )
            ->add('imageTarget', HiddenType::class);

        // adds in each image the path_name that corresponds to the file_name and complete to entity
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $formEvent) {
            /** @var EntityImage */
            $image = $formEvent->getData();
            \dump($formEvent, $image);
            /** @var UploadedFile */
            $file = $formEvent->getForm()->get('path')->getData();
            \dump($file);

            $extraData = $formEvent->getForm()->getExtraData();
            // 1ere Etape
            if ($image !== null && $image->getPath() === \null) {

                /** @var FormBuilderInterface $builder */
                $builder = $formEvent->getForm();
                if ($file !== null) {
                    $image->setPath(\pathinfo($file->getClientOriginalName(), \PATHINFO_BASENAME));
                    // uploads the file for facilitly the work with it
                    $fileName = $this->fileUploaderHelper->upload($file);
                    $image->setFileName($fileName);

                    // Added a hidden field containing the file_name of the uploaded file
                    // and removed the path field to avoid a conflict in downloading the previously downloaded Temp File
                    $builder->add(
                        'file_name',
                        HiddenType::class,
                        [
                            'data' => $image->getFileName()
                        ]
                    )->remove('path')
                        ->add(
                            'path',
                            HiddenType::class,
                            [
                                'data' => \pathinfo($file->getClientOriginalName(), \PATHINFO_BASENAME)
                            ]
                        );
                }
                // if ($extraData) {
                //     $builder->add(
                //         'file_name',
                //         HiddenType::class,
                //         [
                //             'data' => $image->getFileName()
                //         ]
                //     )->remove('path')
                //         ->add(
                //             'path',
                //             HiddenType::class,
                //             [
                //                 'data' => \preg_filter("/-\w+(?=\.\w+$)/", '', $extraData['file_name'])
                //             ]
                //         );
                //     \dump($extraData);
                // }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EntityImage::class,
            'allow_extra_fields' => true
        ]);
    }
}
