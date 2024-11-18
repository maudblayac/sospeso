<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Restaurant;
use App\Entity\Categories;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\ImageType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class AdminProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    
            $builder
            ->add('name', TextType::class, [
                'label' => 'Product Name',
                'required' => true,
                'attr' => ['placeholder' => 'Entrez le nom du produit'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => ['placeholder' => 'Description du produit'],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'required' => true,
                'scale' => 2,
                'attr' => ['placeholder' => 'Entrez le prix du produit'],
            ])
            ->add('image', ImageType::class,[
                'label' => 'Image du produit',
                'is_product' => true,
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',
                'label' => 'Category',
                'required' => true,
            ]);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
    
}
