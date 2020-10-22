<?php

namespace App\Tests\Repository;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testUserFixturesNumber()
    {
        self::bootKernel();
        $this->loadFixtures([TaskFixtures::class, UserFixtures::class]);
        $users = self::$container->get(UserRepository::class)->count([]);
        $this->assertSame(11, $users);
    }
}
