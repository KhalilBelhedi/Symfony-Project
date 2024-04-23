<?php

namespace App\Form;

use App\Entity\Reservation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class Reservation1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('date_debut',DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime_immutable',
            
        ])
          
        ->add('date_fin',DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime_immutable',
           
        ])
            ->add('station', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'localisation',])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
