<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Title'])
            ->add('description', TextType::class, ['label' => 'Description'])
            ->add('address', TextType::class, ['label' => 'Address'])
            ->add('city', TextType::class, ['label' => 'City'])
            ->add('country', TextType::class, ['label' => 'Country'])
            ->add('postalCode', TextType::class, ['label' => 'Postal Code'])
            ->add('imageFile', ImageType::class, [
                'label' => 'Restaurant Image',
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
            ])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'Products',
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
