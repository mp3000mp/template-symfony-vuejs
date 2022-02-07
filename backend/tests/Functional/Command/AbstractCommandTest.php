<?php

namespace App\Tests\Functional\Command;

use App\DataFixtures\AppTestFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends KernelTestCase
{
    protected EntityManagerInterface $em;

    protected Application $application;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->application = new Application($kernel);

        // utils
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // reset database
        $purger = new ORMPurger($this->em, []);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $loader = new ContainerAwareLoader(self::getContainer());
        $loader->addFixture(new AppTestFixtures());
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());

        // parent
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
        unset($this->em);
    }

    protected function getCommandTester(string $commandName): CommandTester
    {
        $command = $this->application->find($commandName);

        return new CommandTester($command);
    }
}
