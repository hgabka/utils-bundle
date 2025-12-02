<?php

namespace Hgabka\UtilsBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'hgabka:fix:admin-locale', description: 'Sets the admin locale for all users to the default admin locale', hidden: false)]
class FixAdminLocaleCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setHelp('The <info>hgabka:fix:admin-locale</info> command can be used to set the admin locale for all users to the default admin locale.');
    }

    /**
     * @param InputInterface  $input  The input
     * @param OutputInterface $output The output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var EntityManager $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        // @var EntityRepository $repo
        $repo = $em->getRepository('KunstmaanAdminBundle:User');
        $users = $repo->findAll();
        $defaultAdminLocale = $this->getContainer()->getParameter('kunstmaan_admin.default_admin_locale');
        foreach ($users as $user) {
            $user->setAdminLocale($defaultAdminLocale);
            $em->persist($user);
        }
        $em->flush();
        $output->writeln('<info>The default admin locale was successfully set for all users.</info>');
    }
}
