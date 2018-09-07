<?php

/**
 * RegisterType form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Class RegisterType
 */
class RegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'firstname',
            TextType::class,
            [
                'label' => 'label.fname',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
                ],
            ]
        );

        $builder->add(
            'lastname',
            TextType::class,
            [
                'label' => 'label.lname',
                'required' => true,
                'attr' => [
                    'max_length' => 128,
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
                            'max' => 32,
                        ]
                    ),
                ],
            ]
        );
    }
}