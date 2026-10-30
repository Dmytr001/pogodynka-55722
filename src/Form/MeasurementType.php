<?php

namespace App\Form;

use App\Entity\Measurement;
use App\Entity\location;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeasurementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('temperature', IntegerType::class, [
                'attr' => [
                    'min' => -100,
                    'max' => 100,
                    'placeholder' => 'np. 22',
                ],
            ])
            ->add('wind_speed', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'placeholder' => 'Prędkość w km/h',
                ],
            ])
            ->add('humidity', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 100,
                    'placeholder' => '%',
                ],
            ])
            ->add('pressure', IntegerType::class, [
                'attr' => [
                    'min' => 0,
                    'placeholder' => 'hPa',
                ],
            ])
            ->add('location', EntityType::class, [
                'class' => Location::class,
                'choice_label' => 'name',
                'placeholder' => 'Wybierz lokalizację...',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Measurement::class,
        ]);
    }
}
