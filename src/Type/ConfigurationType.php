<?php

namespace PrestaShop\Module\BlockWishList\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('WishlistDefaultTitle', TranslatableType::class, [
                // we'll have text area that is translatable
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            ->add('CreateButtonLabel', TranslatableType::class, [
                // we'll have text area that is translatable
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            ->add('WishlistPageName', TranslatableType::class, [
                // we'll have text area that is translatable
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ]);
    }
}
