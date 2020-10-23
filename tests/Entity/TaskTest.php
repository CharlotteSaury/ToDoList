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
    public function testValidTaskEntity()
    {
        $task = new Task();
        $task->setTitle('valid title');
        $task->setContent('valid content');
        $this->assertHasErrors($task, 0);
    }

    /**
     * Assert invalid blank entity (email, company) is invalid.
     *
     * @return void
     */
    public function testInvalidBlankTaskEntity()
    {
        $invalidTask = new Task();
        $invalidTask->setTitle('');
        $invalidTask->setContent('');
        $this->assertHasErrors($invalidTask, 2);        
    }

    /**
     * Assert invalid null entity (email, company) is invalid.
     *
     * @return void
     */
    public function testInvalidNullTaskEntity()
    {
        $invalidTask = new Task();
        $invalidTask->setTitle(null);
        $invalidTask->setContent(null);
        $this->assertHasErrors($invalidTask, 4);  
    }

    /**
     * Test togle method from Task entity
     *
     * @return void
     */
    public function testToggle()
    {
        $task = new Task();
        $task->toggle(!$task->isDone());
        $this->assertTrue($task->isDone());
    }
}
