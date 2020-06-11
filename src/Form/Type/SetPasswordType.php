<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Validator\Constraints\ComplexPattern;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginFormType
 *
 * @package App\Form\Type
 */
class SetPasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password_new', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'security.password.bad_confirm',
                'first_options' => ['label' => 'entity.user.field.password_new'],
                'second_options' => ['label' => 'entity.user.field.password_confirm'],
                'constraints' => [
                    new ComplexPattern([
                        'regexValid' => ['.{8,}'],
                        'regexInvalid' => [],
                        'message' => 'security.password.constraints',
                    ]),
                ],

            ])
            ;
    }
}