<?php

namespace Sbts\Bundle\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class IssueListener
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            $this->setIssueReporter($entity);
        }
    }

    /**
     * Sets issue reporter
     *
     * @param Issue $issue
     */
    protected function setIssueReporter(Issue $issue)
    {
        if (!$issue->getReporter()) {
            $issue->setReporter($this->container->get('security.context')->getToken()->getUser());
        }
    }

    /**
     * Set Issue code based on Id and Organization
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Issue) {
            $entity->generateCode();
        }
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {

    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {

    }
}
