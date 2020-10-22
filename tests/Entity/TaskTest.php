<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Tests\Utils\AssertHasErrors;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    use AssertHasErrors;
    use FixturesTrait;

    /**
     * Assert valid entity is valid.
     *
     * @return void
     */
    public function testValidEntity()
    {
        $task = new Task();
        $task->setTitle('valid title');
        $task->setContent('valid content');
        $this->assertHasErrors($task, 0);
    }

    /**
     * Assert invalid entity (email, company) in invalid.
     *
     * @return void
     */
    public function testInvalidEntity()
    {
        $invalidTask = new Task();
        $invalidTask->setTitle('');
        $invalidTask->setContent('');
        $this->assertHasErrors($invalidTask, 2);
    }

    public function testToggle()
    {
        $task = new Task();
        $task->toggle(!$task->isDone());
        $this->assertTrue($task->isDone());
    }
}
