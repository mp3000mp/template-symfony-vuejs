<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginFormType
 * @package App\Form\Type
 */
class LoginFormType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'entity.user.field.username',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'entity.user.field.password',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'form.action.submit',
            ])
            ;

    }
}
