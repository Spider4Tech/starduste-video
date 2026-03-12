<?php

namespace App\Form;

use App\Entity\Utilisateurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Pseudo')
            ->add('SUBSCRIBERS')
            ->add('JOIN_DATE')
            ->add('UPLOADED_VIDEO')
            ->add('IS_ADMIN')
            ->add('AGE')
            ->add('PASSWORD')
            ->add('EMAIL')
            ->add('IP_ADRESSE')
            ->add('LAST_LOGIN')
            ->add('pfppath')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateurs::class,
        ]);
    }
}
