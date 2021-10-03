<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\ImageType;
use App\Entity\Category;
use App\Entity\Contribution;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ContributionType extends AbstractType
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

        /** @var Contribution */
        $contribution = $options['data'];

        $imagesInRequest = $request->get('contribution')['images'] ?? \false;
        $filesInResquest = $request->files->get('contribution')['images'] ?? \false;

        // See if contribution contains the data of one Trick
        /** @var Trick */
        $isExistantTrick = $contribution->getTrick() ?? \false;

        $this->trick = $contribution->getTrick();
        
        /** @var Image */
        $imagesInContribution = $contribution->getImages();

        if (!$isExistantTrick) {
            $builder
                ->add(
                    'title',
                    TextType::class,
                    [
                        'label' => 'Nom de la figure',
                        'attr' => [
                            'placeholder' => 'Tapez ici le nom',
                            'maxlength' => 70
                        ]
                    ]
                )
                ->add(
                    'category',
                    EntityType::class,
                    [
                        'label' => 'Category',
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
                        'maxlength' => 160
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
            )
            ->add(
                'videos',
                CollectionType::class,
                [
                    'entry_type' => VideoType::class,
                    'entry_options' => ['label' => false],
                    'attr' => ['class' => 'path_field'],
                    'label' => false,
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );

        $choiceList = [];
        $choiceAttr = [];

        if ($imagesInRequest) {
            foreach ($imagesInRequest as $key => $image) {
                if (\key_exists('in_front', $image) && !$image['in_front']) {
                    if ($image['imageTarget'] && \key_exists('title', $image)) {
                        $choiceList[$image['title']] = $key;
                        $choiceAttr[$image['title']] = ['data-id' => $key];
                    }
                } else if (!\key_exists('in_front', $image)) {
                    /** @var UploadedFile */
                    $file = $filesInResquest[$key]['file_name'];
                    $choiceList[$file->getClientOriginalName()] = $key;
                    $choiceAttr[$file->getClientOriginalName()] = ['data-id' => $key];
                }
            }
        } else {
            /** @var Image $image*/
            foreach ($imagesInContribution as $key => $image) {
                $choiceList[$image->getOriginalFileName()] = $key;
                $choiceAttr[$image->getOriginalFileName()] = ['data-id' => $key];
            }
        }

        $builder->add(
            'image_in_front',
            ChoiceType::class,
            [
                'mapped' => false,
                'label' => 'Image mise en avant',
                'placeholder' => '-- Choisir l\'image mise en avant --',
                'choices' => $choiceList,
                'choice_attr' => $choiceAttr,
                'required' => \false,
            ]
        )
            // reset the ChoiceToValueTransformer to allow for my list custom
            ->get('image_in_front')->resetViewTransformers();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contribution::class,
        ]);
    }
}
