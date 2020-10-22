<?php

namespace App\Tests\Repository;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\TaskRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testTaskFixturesNumber()
    {
        self::bootKernel();
        $this->loadFixtures([TaskFixtures::class, UserFixtures::class]);
        $tasks = self::$container->get(TaskRepository::class)->count([]);
        $this->assertSame(10, $tasks);
    }
}
