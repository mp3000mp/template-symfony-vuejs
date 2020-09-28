<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Service\SingleSignOn\SSOService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class LoginFormType.
 */
class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => ['autofocus' => true],
                'label' => 'entity.user.field.username',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'entity.user.field.password',
            ])
            ->add(SSOService::SESSION_SP_URL_KEY, HiddenType::class, [
            ])
            ;
    }
}
