<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    /**
     * Load task fixtures.
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 10; ++$i) {
            $task = new Task();
            $task->setTitle('tache '.$i)
                ->setContent($faker->text(mt_rand(50, 150)));
            if ($i > 5) {
                $task->setIsDone(true);
            }
            $manager->persist($task);
        }
        
        $manager->flush();
    }
}