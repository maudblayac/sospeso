<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Restaurant;

class AccountSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Vous pouvez ajouter d'autres champs de paramètres de compte si nécessaire
            ->add('isPaused', CheckboxType::class, [
                'label' => 'Mettre mon annonce en pause',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Le formulaire est lié à l'entité Restaurant
            'data_class' => Restaurant::class,
        ]);
    }
}
