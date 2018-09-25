<?php
/**
 * DeleteSongType Form.
 */

namespace Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DeleteSongType
 *
 * Class DeleteSongType
 * @package Form
 */
class DeleteSongType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'delete',
            SubmitType::class,
            [
                'label' => 'label.delete',
                /*'required' => true,*/
                /*'constraints' => [
                    new Assert\NotBlank(),
                ],*/
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'delete_type';
    }
}