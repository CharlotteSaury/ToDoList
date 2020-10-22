<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\Utils\AssertHasErrors;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use AssertHasErrors;
    use FixturesTrait;

    /**
     * Create a valid entity for tests.
     *
     * @return User
     */
    public function getEntity(): User
    {
        $user = new User();
        $user->setEmail('valid@email.com');
        $user->setUsername('ValidUsername');
        $user->setPassword('password');

        return $user;
    }

    /**
     * Assert valid entity is valid.
     *
     * @return void
     */
    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    /**
     * Assert invalid entity (email, company) in invalid.
     *
     * @return void
     */
    public function testInvalidEntity()
    {
        $invalidUser = $this->getEntity();
        $invalidUser->setEmail('invalidUser.com');
        $invalidUser->setUsername('');
        $this->assertHasErrors($invalidUser, 2);
    }

    /**
     * Assert User unicity with email.
     *
     * @return void
     */
    public function testInvalidUniqueEmail()
    {
        $this->loadFixtureFiles([
            dirname(__DIR__).'/Fixtures/Users.yaml',
        ]);
        $invalidUser = $this->getEntity();
        $invalidUser->setEmail('user1@email.com');
        $this->assertHasErrors($invalidUser, 1);
    }

    public function testAddAndRemoveTask()
    {
        $user = new User();
        for ($i = 1; $i <= 10; ++$i) {
            $task = new Task();
            $task->setTitle('task'.$i)
                ->setContent('content'.$i)
                ->setAuthor($user);
            $user->addTask($task);
        }
        $tasks = $user->getTasks();
        $this->assertSame(10, \count($tasks));

        $user->removeTask($tasks[0]);
        $user->removeTask($tasks[1]);
        $this->assertSame(8, \count($tasks));
    }
}
