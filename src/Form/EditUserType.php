<?php

/**
 * EditUserType form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class EditUserType
 */
class EditUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'fname',
            TextType::class,
            [
                'label' => 'label.fname',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'min' => 3,
                            'max' => 128,
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'lname',
            TextType::class,
            [
                'label' => 'label.lname',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'min' => 3,
                            'max' => 128,
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'email',
            EmailType::class,
            [
                'label' => 'label.email',
                'required' => true,
                'attr' => [
                    'max_length' => 32,
                    'class' => 'form-row'

                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                    new Assert\Length(
                        [
                            'min' => 5,
                            'max' => 128,
                        ]
                    ),
                ],
            ]
        );

        /*        $builder->add(
                    'bday',
                    BirthdayType::class,
                    [
                        'label' => 'label.bday',
                        'required' => false,
                        'attr' => [
                            'max_length' => 128,
                        ],
                    ]
                );*/

        $builder->add(
            'country',
            CountryType::class,
            [
                'label' => 'label.country',
                'required' => false,
                'attr' => [
                    'max_length' => 128,
                ],
            ]
        );

        /*$builder->add(
            'login',
            TextType::class,
            [
                'label' => 'label.login',
                'required' => true,
                'attr' => [
                    'max_length' => 32,
                    'class' => 'form-row'

                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'min' => 5,
                            'max' => 45,
                        ]
                    ),
                ],
            ]
        );*/

        /*$builder->add(
            'password',
            RepeatedType::class,
            [
                'type' => PasswordType::class,
                'first_options' => array('label' => 'label.password'),
                'second_options' => array('label' => 'label.repeatPassword'),
                'required' => true,
                'attr' => [
                    'max_length' => 32,
                    'class' => 'form-row'

                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(
                        [
                            'min' => 8,
                            'max' => 255,
                        ]
                    ),
                ],
            ]
        );*/
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'editUser_type';
    }
}