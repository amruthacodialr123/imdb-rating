<?php

namespace App\Form;

use App\Entity\Movies;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Category;
use Symfony\Component\Validator\Constraints as Assert;

class MoviesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['label' => 'Movie Title']) // Use 'null' as the field type for a simple text input
            ->add('release_date')
            ->add('image', FileType::class, [
                'label' => 'Movie Image',
                'mapped' => false,
            ])
            ->add('category', null, ['label' => 'Movie category']) // Use 'null' as the field type for a simple text input

           
            ->add('description', TextareaType::class)
            ->add('director')
            ->add('rating')
            ->add('budget');

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movies::class,
            ]);
    }
}
