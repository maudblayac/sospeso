<?php

namespace App\Form;

use App\DTO\RestaurantSearchDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categories', ChoiceType::class, [
                'choices' => [
                    // Remplacer par les catégories de restaurant réelles
                    'Vegan' => 'vegan',
                    'Café' => 'cafe',
                ],
                'placeholder' => 'Catégorie',
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Ville'],
            ])
            ->add('minPrice', IntegerType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Min €'],
            ])
            ->add('maxPrice', IntegerType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Max €'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RestaurantSearchDto::class,
            'method' => 'GET',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
