<?php

namespace App\Form;

use App\Entity\Video;
use App\Service\EmbedlyUrlHelper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Util\ServerParams;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;

class VideoType extends AbstractType
{

    private $embedlyUrlHelper;

    public function __construct(EmbedlyUrlHelper $embedlyUrlHelper)
    {
        $this->embedlyUrlHelper = $embedlyUrlHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) {

            $builder = $formEvent->getForm();
            \dump($formEvent);

            /** @var Video */
            $existingVideo = $formEvent->getData();

            $builder
                ->add(
                    'title',
                    TextType::class,
                    [
                        'label' => 'Titre de la vidéo',
                        'attr' => [
                            'placeholder' => 'Intitulé de la vidéo',
                            'maxlength' => 70
                        ]
                    ]
                )
                ->add('videoTarget', HiddenType::class);

            if ($existingVideo) {
                $builder->add('link', HiddenType::class);
            } else {
                $builder->add(
                    'link',
                    UrlType::class,
                    [
                        'label' => 'Lien de la vidéo',
                        'attr' => [
                            'placeholder' => 'Coller ici le lien de la vidéo',
                            'pattern' => "https:\/\/(?:www\.)?[(youtube\.com)|(youtu\.be)|(dailymotion\.com)|(vimeo\.com)].+",
                            'title' => "Format d'URL accepté : Youtube / Dailymotion / Vimeo"
                        ],
                        'constraints' => [
                            new Regex([
                                'pattern' => "/(?::\/\/w{0,3}.*)((youtube|youtu)(?:.(?:com|be)\/))|(dailymotion\.com)|(vimeo\.com)/",
                                'message' => "Format d'URL accepté : Youtube / Dailymotion / Vimeo"
                            ])
                        ]
                    ]
                );
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $formEvent) {
            /** @var Video */
            $video = $formEvent->getData();
            // Limit the number of characters in the title
            if (\strlen($video->getTitle()) > 70) {
                $video->setTitle(\substr($video->getTitle(), 0, 70));
            }
            $video->setLink($this->embedlyUrlHelper->embed($video->getLink()));
            \dump($video->getLink());
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class
        ]);
    }
}
