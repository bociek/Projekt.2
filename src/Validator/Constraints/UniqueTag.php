<?php
/**
 * Unique Tag constraint.
 */
namespace Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueTag.
 */
class UniqueTag extends Constraint
{
    /**
     * Message.
     *
     * @var string $message
     */
    public $message = '{{ tag }}'.'value.not.unique';

}