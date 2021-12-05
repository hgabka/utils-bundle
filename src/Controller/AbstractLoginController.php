<?php

namespace Hgabka\UtilsBundle\Controller;

use Hgabka\UtilsBundle\Form\AdminLoginForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

abstract class AbstractLoginController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    protected $authenticationUtils;

    /**
     * @required
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return AbstractLoginController
     */
    public function setAuthenticationUtils(AuthenticationUtils $authenticationUtils): self
    {
        $this->authenticationUtils = $authenticationUtils;

        return $this;
    }

    public function loginAction(): Response
    {
        $form = $this->createForm(AdminLoginForm::class, [
            'email' => $this->authenticationUtils->getLastUsername(),
        ]);

        return $this->render('@HgabkaUtils/security/login.html.twig', [
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'form' => $form->createView(),
            'error' => $this->authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    public function logoutAction(): void
    {
        // Left empty intentionally because this will be handled by Symfony.
    }
}
