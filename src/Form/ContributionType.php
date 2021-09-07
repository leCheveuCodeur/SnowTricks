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
                                'placeholder' => 'Tapez ici le nom',
                                'maxlength'=> 70
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
                            'placeholder' => $isExistantTrick ? '' : 'Tapez ici l\'intro',
                            'maxlength'=> 160
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
                        'entry_options' => ['label' => false],
                        'attr' => ['class' => 'path_field'],
                        'label' => false,
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true,
                    ]
                );
        });

        // $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
        //     $data = $event->getData();
        //     $images = $data['images'];
        //     $oldKey = '';
        //     foreach ($images as $key => $image) {
        //         if ($key < $oldKey) {
        //             \dump($key);
        //             unset($images[$key]);
        //             $event->getForm()->get('images')->offsetUnset($key);
        //         }
        //         $oldKey = $key;
        //     }

            // \dd($images, $event, $event->getForm()->get('images'));

            // /** @var UploadedFile */
            // $file = $event->getForm()->get('path')->getData();
            // if ($image !== null && $image->getPath() === \null && $file !== null) {
            //     $image->setPath(\pathinfo($file->getClientOriginalName(), \PATHINFO_BASENAME));
            // }
        // });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contribution::class,
        ]);
    }
}
