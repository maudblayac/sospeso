<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminRestaurateurProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Ajoutez les champs que vous souhaitez modifier
            ->add('firstname', null, ['label' => 'Prénom'])
            ->add('lastname', null, ['label' => 'Nom'])
            ->add('phoneNumber', null, ['label' => 'Numéro de téléphone'])
            ->add('address', null, ['label' => 'Adresse'])
            ->add('city', null, ['label' => 'Ville'])
            ->add('postalCode', null, ['label' => 'Code postal'])
            ->add('country', null, ['label' => 'Pays'])
            ->add('email', null, ['label' => 'Email'])
            ->add('website', null, ['label' => 'Site web'])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
