<?php

namespace App\Form;

use App\Entity\Video;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Video as VideoConstraint;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

class VideoUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('videoFile', FileType::class, [
                'label' => 'Video (MP4)',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File(
                        maxSize: '40G',
                        mimeTypes: ['video/mp4']

                    ),
                    /**new VideoConstraint(
                        maxPixels: 2073600,

                    )*/
                ]

            ])
        ->add('status', CheckboxType::class, [
            'label' => 'Vidéo publique',
             'mapped' => true,
            'required' => false,
            'data' => true,
    ])
            ->add('categorie', ChoiceType::class, [
                'label'       => 'Catégorie',
                'placeholder' => 'Choisir une catégorie...',
                'required'    => true,
                'choices'     => [
                    ' Tech & Code'              => 'tech',
                    ' Gaming'                   => 'gaming',
                    ' Musique'                  => 'music',
                    ' Art & Design'             => 'art',
                    'Cinéma & Courts-métrages' => 'cinema',
                    ' Voyage'                   => 'travel',
                    ' Éducation'                => 'education',
                    'Science'                  => 'science',
                    ' Sport'                    => 'sport',
                    ' Cuisine'                  => 'cuisine',
                    ' Lifestyle'                => 'lifestyle',
                    ' Humour'                   => 'humour',
                    ' Autre'                    => 'other',
                ],
                'constraints' => [
                    new NotBlank(message: 'Veuillez choisir une catégorie.')
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'constraints'=> [
                    new Length(
                        max:  1024,
                        maxMessage: "la description ne peut pas faire plus de 1024 caractères"
                    )
                ],
                'attr'=> ['maxlength' => 1024, 'rows' => 4],
            ])
        ->add('thumbnailFile', FileType::class, [
        'label'    => 'Miniature',
        'mapped'   => false,
        'required' => false,
        'constraints' => [
            new File(
                maxSize: '10M',
                mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                mimeTypesMessage: 'Formats acceptés : JPG, PNG, WEBP.',
            ),
        ],
    ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
            'csrf_protection'=> false,
            'csrf_field_name'=> '_token',
            'csrf_token_id'=> 'submit'
        ]);
    }
}
