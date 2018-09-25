<?php

/**
 * AddChapterType form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AddChapterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'author',
            TextType::class,
            [
                'label' => 'label.author',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'min' => 2,
                            'max' => 128,
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'chapter_title',
            TextType::class,
            [
                'label' => 'label.chapter_title',
                'required' => false,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'min' => 4,
                            'max' => 128,
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'audiobook_title',
            TextType::class,
            [
                'label' => 'label.audiobook_title',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'min' => 4,
                            'max' => 128,
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'chapter_id',
            IntegerType::class,
            [
                'label' => 'label.track_id',
                'required' => false,
                'data' => 1,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'max' => 64,
                        ]
                    ),
                ],
            ]
        );


        $builder->add(
            'year',
            IntegerType::class,
            [
                'label' => 'label.year',
                'required' => false,
                'data' => 2018,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'max' => 4,
                        ]
                    ),
                ],
            ]
        );

    }
}