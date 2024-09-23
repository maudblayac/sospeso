<?php
namespace App\Form;

use App\Entity\Categories;
use App\Entity\Product;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DecimalType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product Name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Entrez le nom du produit'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description du produit',
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'required' => true,
                'scale' => 2,  
                'attr' => [
                    'placeholder' => 'Entrez le prix du produit',
                ],
            ])
            // ->add('picture', TextType::class, [
            //     'label' => 'Picture URL',
            //     'required' => false,
            //     'attr' => [
            //         'placeholder' => 'Image'
            //     ],
            // ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => 'name',  
                'label' => 'Category',
                'required' => true,
            ]);
            // ->add('restaurant', EntityType::class, [
            //     'class' => Restaurant::class,
            //     'choice_label' => 'name',  
            //     'label' => 'Restaurant',
            //     'required' => true,
            // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
