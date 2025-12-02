<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecaptchaType extends AbstractType
{
    public function __construct(private readonly ?string $siteKey)
    {
    }

    public function getParent(): string
    {
        return FormType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sitekey' => $this->siteKey,
            'error_bubbling' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'recaptcha';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['sitekey'] = $options['sitekey'];
    }
}
