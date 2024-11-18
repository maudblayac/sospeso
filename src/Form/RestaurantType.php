<?php

namespace App\Form;

use App\Entity\Restaurant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'Prénom',
                'attr' => ['class' => 'formInput'],
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Nom',
                'attr' => ['class' => 'formInput'],
            ])
            ->add('siren', TextType::class, [
                'required' => true,
                'label' => 'SIREN',
                'attr' => ['class' => 'formInput'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
