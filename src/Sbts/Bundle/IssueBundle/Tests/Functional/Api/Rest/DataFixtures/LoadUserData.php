<?php

namespace Sbts\Bundle\IssueBundle\Tests\Functional\Api\Rest\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\Bundle\UserBundle\Entity\UserApi;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface
{
    const USER_NAME = 'user_no_permissions';
    const USER_PASSWORD = 'user_api_key';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        /** @var \Oro\Bundle\UserBundle\Entity\UserManager $userManager */
        $userManager = $this->container->get('oro_user.manager');

        $role = $userManager
            ->getStorageManager()
            ->getRepository('OroUserBundle:Role')
            ->findOneBy(['role' => 'IS_AUTHENTICATED_ANONYMOUSLY']);

        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();

        /** @var \Oro\Bundle\UserBundle\Entity\User $user */
        $user = $userManager->createUser();

        $api = new UserApi();
        $api
            ->setApiKey('user_api_key')
            ->setOrganization($organization)
            ->setUser($user);

        $user
            ->setUsername(self::USER_NAME)
            ->setPlainPassword(self::USER_PASSWORD)
            ->setSalt('')
            ->setFirstName('Test')
            ->setLastName('User')
            ->addRole($role)
            ->setEmail('test@example.com')
            ->addApiKey($api)
            ->setOrganization($organization)
            ->addOrganization($organization);

        $userManager->updateUser($user);
    }
}
