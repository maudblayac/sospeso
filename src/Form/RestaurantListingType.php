<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Restaurant;
use App\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RestaurantListingType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        // Obtenez l'utilisateur connecté via le TokenStorage
        $user = $this->tokenStorage->getToken()->getUser();
        $restaurant = $user->getRestaurant();

        $builder
            ->add('title', TextType::class, ['label' => 'Title'])
            ->add('description', TextType::class, ['label' => 'Description'])
            ->add('image', ImageType::class, [
                'label' => 'Restaurant Image',
                'required' => false,
            ])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => 'Products',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (ProductRepository $repository) use ($restaurant) {
                    return $repository->createQueryBuilder('p')
                        ->where('p.restaurant = :restaurant')
                        ->setParameter('restaurant', $restaurant);
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
