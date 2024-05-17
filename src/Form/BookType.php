<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre *'
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur *'
            ])
        ;

        if($options['isEditing'])
        {
            $builder
            ->add('isbn', TextType::class, [
                'required' => false,
                'label' => 'ISBN'
            ])
            ->add('publishedAt', TextType::class, [
                'required' => false,
                'label' => 'Date de publication'
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'isEditing' => false
        ]);
    }
}
