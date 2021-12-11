<?php

namespace Hgabka\UtilsBundle\Helper\AdminPanel;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultAdminPanelAdaptor implements AdminPanelAdaptorInterface
{
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return AdminPanelActionInterface[]
     */
    public function getAdminPanelActions()
    {
        return [
            $this->getLanguageChooserAction(),
            $this->getChangePasswordAction(),
            $this->getLogoutAction(),
        ];
    }

    protected function getLanguageChooserAction()
    {
        return new AdminPanelAction(
            [],
            '',
            '',
            '@HgabkaUtilsBundle/AdminPanel/_language_chooser.html.twig'
        );
    }

    protected function getChangePasswordAction()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return new AdminPanelAction(
            [
                'path' => 'KunstmaanAdminBundle_user_change_password',
                'params' => ['id' => $user->getId()],
            ],
            ucfirst($user->getUsername()),
            'user'
        );
    }

    protected function getLogoutAction()
    {
        return new AdminPanelAction(
            [
                'path' => 'admin_logout',
                'attrs' => ['id' => 'app__logout', 'title' => 'logout'],
            ],
            '',
            'sign-out'
        );
    }
}
