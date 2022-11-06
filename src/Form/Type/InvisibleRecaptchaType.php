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

    public function getParent()
    {
        return FormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'sitekey' => $this->siteKey,
            'action' => 'utils',
            'error_bubbling' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'invisible_recaptcha';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['sitekey'] = $options['sitekey'];
        $view->vars['action'] = $options['action'];
    }
}
