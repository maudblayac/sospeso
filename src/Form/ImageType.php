<?php

namespace App\Form;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Détermine si on utilise l'image de produit ou de restaurant
        $imageField = $options['is_product'] ? 'productImageFile' : 'restaurantImageFile';

        $builder
            ->add($imageField, VichFileType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => 'Choisissez une image'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'is_product' => true, 
        ]);

        $resolver->setAllowedTypes('is_product', 'bool');
    }
}
