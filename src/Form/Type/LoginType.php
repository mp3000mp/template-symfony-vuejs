<?php

namespace App\Form\Type;

use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class LoginFormType
 * @package App\Form\Type
 */
class LoginType extends AbstractType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        //$resolver->setRequired('captcha');
    }

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
            ;
        /*if($options['captcha']){
            $builder->add('captcha', CaptchaType::class, [
                'height' => 40,
                'length' => 6,
            ]);
        }*/
    }
}
