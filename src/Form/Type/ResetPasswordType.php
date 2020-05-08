<?php

namespace App\Form\Type;

use App\Validator\Constraints\ComplexPattern;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LoginFormType
 * @package App\Form\Type
 */
class ResetPasswordType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('password_current', PasswordType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'entity.user.field.password_current',
            ])
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
