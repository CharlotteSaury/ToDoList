<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserTestFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * Load user fixtures to database.
     *
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 2; ++$i) {
            $user = new User();
            $user->setEmail('user'.$i.'@email.com')
                ->setUsername('user'.$i)
                ->setPassword($this->encoder->encodePassword($user, 'password'));
            $manager->persist($user);
            $this->addReference('user'.$i, $user);
        }

        $admin = new User();
        $admin->setEmail('admin1@email.com')
            ->setUsername('admin1')
            ->setPassword($this->encoder->encodePassword($user, 'password'))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);
        $this->addReference('admin'.$i, $admin);

        $manager->flush();
    }
}
