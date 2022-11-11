<?php

namespace Hgabka\UtilsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvisibleRecaptchaType extends AbstractType
{
    public function __construct(protected readonly ?string $siteKey)
    {
    }

    public function getParent(): string
    {
        return FormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sitekey' => $this->siteKey,
            'action' => 'utils',
            'error_bubbling' => false,
            'label' => false,
            'mapped' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'invisible_recaptcha';
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['sitekey'] = $options['sitekey'];
        $view->vars['action'] = $options['action'];
    }
}
