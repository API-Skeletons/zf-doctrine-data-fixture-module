<?php

declare(strict_types=1);

namespace ZF\Doctrine\DataFixture\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZF\Doctrine\DataFixture\DataFixtureManager;
use ZF\Doctrine\DataFixture\DataFixtureManagerFactory;

abstract class AbstractCommand extends Command
{

    const ARGUMENT_GROUP = 'group_name';

    /**
     * @var ServiceLocatorInterface
     */
    protected $container;

    /**
     * Command constructor
     *
     * @param ServiceLocatorInterface $container
     */
    public function __construct(ServiceLocatorInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $this->executeCommand($input, $output);
        } catch (ServiceNotCreatedException $exception) {
            $interface = new SymfonyStyle($input, $output);
            $interface->error($exception->getPrevious()->getMessage());
        }
    }

    /**
     * Execute the command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    abstract protected function executeCommand(InputInterface $input, OutputInterface $output): void;

    /**
     * Get the data fixture manager
     *
     * @param string $groupName
     *
     * @return DataFixtureManager
     */
    protected function getDataFixtureManager(string $groupName): DataFixtureManager
    {
        return $this->container->build(DataFixtureManager::class, [
            DataFixtureManagerFactory::OPTION_GROUP => $groupName,
        ]);
    }
}
