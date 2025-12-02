<?php

declare(strict_types=1);

namespace Hgabka\UtilsBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\Exception\RuntimeCommandException;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\Util\ClassNameDetails;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractUserRoleCommand extends Command
{
    public function __construct(protected readonly EntityManagerInterface $manager, protected readonly ?Generator $generator = null)
    {
        parent::__construct();
    }

    public function interact(InputInterface $input, OutputInterface $output): void
    {
        $io = new ConsoleStyle($input, $output);

        $argument = $this->getDefinition()->getArgument('name');
        $question = $this->createEntityClassQuestion($argument->getDescription());
        $entityClassName = $io->askQuestion($question);

        $input->setArgument('name', $entityClassName);
        $questions = [];

        if (!$input->getArgument('property')) {
            $question = new Question('Please provide the property that is used for identifying a user', 'email');
            $question->setValidator(function ($property) {
                if (empty($property)) {
                    throw new \Exception('Identifier can not be empty');
                }

                return $property;
            });

            $questions['property'] = $question;
        }
        if (!$input->getArgument('identifier')) {
            $question = new Question('Please provide the identifier of the user');
            $question->setValidator(function ($username) {
                if (empty($username)) {
                    throw new \Exception('Identifier can not be empty');
                }

                return $username;
            });
            $questions['identifier'] = $question;
        }

        if (!$input->getArgument('role')) {
            $question = new Question('Please choose a role:');
            $question->setValidator(function ($role) {
                if (empty($role)) {
                    throw new \Exception('Role can not be empty');
                }

                return $role;
            });
            $questions['role'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $io->askQuestion($question);
            $input->setArgument($name, $answer);
        }
    }

    public function getEntitiesForAutocomplete(): array
    {
        $entities = [];

        $allMetadata = $this->getMetadata();

        foreach (array_keys($allMetadata) as $classname) {
            $entityClassDetails = new ClassNameDetails($classname, 'App\\Entity\\');
            $entities[] = $entityClassDetails->getRelativeName();
        }

        sort($entities);

        return $entities;
    }

    public function validateUserClass(string $value = null): string
    {
        if (null === $value || '' === $value) {
            throw new RuntimeCommandException('This value cannot be blank.');
        }

        return $value;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL, 'Class name of the user to promote (e.g. <fg=yellow>App\Entity\User</>)')
            ->addArgument('property', InputArgument::OPTIONAL, 'The property of the user that is used as identifier (username, email)')
            ->addArgument('identifier', InputArgument::OPTIONAL, 'The identifier of the user to be promoted')
            ->addArgument('role', InputArgument::OPTIONAL, 'The role to add')
        ;
    }

    protected function getMetadata(): array
    {
        $cmf = $this->manager->getMetadataFactory();
        $metadata = [];

        foreach ($cmf->getAllMetadata() as $m) {
            if (is_a($m->getName(), UserInterface::class, true)) {
                $metadata[$m->getName()] = $m;
            }
        }

        return $metadata;
    }

    protected function getUserFromArguments(InputInterface $input): UserInterface
    {
        if (null === $this->generator) {
            throw new RuntimeCommandException('MakerBundle is needed for the command to work. Try running in dev environment');
        }
        $identifier = $input->getArgument('identifier');

        $property = $input->getArgument('property');

        $entityClassDetails = $this->generator->createClassNameDetails(
            $input->getArgument('name'),
            'Entity\\'
        );

        if (!is_a($entityClassDetails->getFullName(), UserInterface::class, true)) {
            throw new RuntimeCommandException('The class must be an instance of UserInterface');
        }

        $repository = $this->manager->getRepository($entityClassDetails->getFullName());
        $user = $repository?->findOneBy([$property => $identifier]);

        if (!$user instanceof UserInterface) {
            throw new RuntimeCommandException('User cannot be found');
        }

        return $user;
    }

    private function createEntityClassQuestion(string $questionText): Question
    {
        $question = new Question($questionText);
        $question->setValidator($this->validateUserClass(...));
        $question->setAutocompleterValues($this->getEntitiesForAutocomplete());

        return $question;
    }
}
