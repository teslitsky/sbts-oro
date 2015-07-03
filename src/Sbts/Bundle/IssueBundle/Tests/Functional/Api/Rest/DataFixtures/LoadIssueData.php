<?php

namespace Sbts\Bundle\IssueBundle\Tests\Functional\Api\Rest\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class LoadIssueData extends AbstractFixture implements ContainerAwareInterface
{
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
        $user = $manager->getRepository('OroUserBundle:User')->findOneBy(['username' => 'admin']);

        if (!$user) {
            return;
        }

        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();

        $issue = new Issue();
        $issue
            ->setSummary('Test ACL issue')
            ->setCode('SBTS-1')
            ->setDescription('Test ACL issue description')
            ->setReporter($user)
            ->setOwner($user)
            ->setOrganization($organization)
            ->addCollaborator($user);

        $manager->persist($issue);
        $manager->flush();
    }
}
