<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Oro\Bundle\UserBundle\Entity\UserInterface;
use Oro\Bundle\WorkflowBundle\Model\WorkflowManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class LoadIssueEntityData extends AbstractFixture implements DependentFixtureInterface, ContainerAwareInterface
{
    /**
     * @var WorkflowManager
     */
    protected $workflowManager;

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @var array
     */
    protected $issues = [
        [
            'summary'     => 'Neque porro quisquam',
            'description' => 'Est qui dolorem ipsum quia dolor sit amet, adipisci velit',
            'type'        => Issue::TYPE_BUG,
            'priority'    => Issue::PRIORITY_BLOCKER,
        ],
        [
            'summary'     => 'Sed ut perspiciatis unde omnis',
            'description' => 'Iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam',
            'type'        => Issue::TYPE_TASK,
            'priority'    => Issue::PRIORITY_TRIVIAL,
        ],
        [
            'summary'     => 'Nemo enim ipsam voluptatem',
            'description' => 'Quia voluptas sit aspernatur aut odit aut fugit',
            'type'        => Issue::TYPE_STORY,
            'priority'    => Issue::PRIORITY_MINOR,
            'subtasks'    => [
                [
                    'summary'     => 'At vero eos et accusamus et iusto',
                    'description' => 'Odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti',
                    'type'        => Issue::TYPE_SUB_TASK,
                    'priority'    => Issue::PRIORITY_MAJOR,
                ],
                [
                    'summary'     => 'Atque corrupti quos dolores et quas molestias',
                    'description' => 'Excepturi sint occaecati cupiditate non provident',
                    'type'        => Issue::TYPE_SUB_TASK,
                    'priority'    => Issue::PRIORITY_CRITICAL,
                ],
                [
                    'summary'     => 'Similique sunt in culpa qui officia deserunt mollitia animi',
                    'description' => 'Et harum quidem rerum facilis est et expedita distinctio.',
                    'type'        => Issue::TYPE_SUB_TASK,
                    'priority'    => Issue::PRIORITY_MINOR,
                ],
            ],
        ],
        [
            'summary'     => 'Nam libero tempore, cum soluta nobis',
            'description' => 'Est eligendi optio cumque nihil impedit quo minus id quod maximo',
            'type'        => Issue::TYPE_BUG,
            'priority'    => Issue::PRIORITY_MAJOR,
        ],
    ];

    /**
     * @var array
     */
    protected $workflowSteps = [
        'open',
        'start_progress',
        'resolve',
        'close',
    ];

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Sbts\Bundle\IssueBundle\Migrations\Data\Demo\ORM\LoadUserData',
            'Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData',
        ];
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->workflowManager = $container->get('oro_workflow.manager');
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueTypeRepoClass = ExtendHelper::buildEnumValueClassName('issue_type');
        $issuePriorityRepoClass = ExtendHelper::buildEnumValueClassName('issue_priority');

        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
        $reporter = $manager
            ->getRepository('OroUserBundle:User')
            ->findOneBy(['username' => 'admin']);

        $owner = $manager
            ->getRepository('OroUserBundle:User')
            ->findOneBy(['username' => 'manager']);

        if (!$organization || !$reporter || !$owner) {
            return;
        }

        foreach ($this->issues as $issue) {
            $entity = new Issue();
            $entity = $this->fillEntity($entity, $issue, $reporter, $owner, $organization);
            $entity
                ->setIssueType($manager->getRepository($issueTypeRepoClass)->find($issue['type']))
                ->setIssuePriority($manager->getRepository($issuePriorityRepoClass)->find($issue['priority']));

            $manager->persist($entity);
            $manager->flush();

            $this->setupIssueStatus($entity);
            $manager->flush();

            if (isset($issue['subtasks'])) {
                foreach ($issue['subtasks'] as $data) {
                    $subTask = new Issue();
                    $subTask = $this->fillEntity($subTask, $data, $reporter, $owner, $organization);
                    $subTask->setParent($entity);
                    $subTask
                        ->setIssueType($manager->getRepository($issueTypeRepoClass)->find($data['type']))
                        ->setIssuePriority($manager->getRepository($issuePriorityRepoClass)->find($data['priority']));

                    $manager->persist($subTask);
                    $manager->flush();

                    $this->setupIssueStatus($subTask);
                    $manager->flush();
                }

            }
        }
    }

    /**
     * @param Issue                 $entity
     * @param array                 $data
     * @param UserInterface         $reporter
     * @param UserInterface         $owner
     * @param OrganizationInterface $organization
     *
     * @return Issue
     */
    protected function fillEntity(
        Issue $entity,
        $data,
        UserInterface $reporter,
        UserInterface $owner,
        OrganizationInterface $organization
    ) {
        $entity
            ->setSummary($data['summary'])
            ->setDescription($data['description'])
            ->setCreatedAt($this->getRandomDate())
            ->setUpdatedAt($this->getRandomDate())
            ->setReporter($reporter)
            ->setOwner($owner)
            ->setOrganization($organization)
            ->addCollaborator($reporter)
            ->addCollaborator($owner);

        return $entity;
    }

    /**
     * @param Issue $issue
     */
    protected function setupIssueStatus(Issue $issue)
    {
        $nextStep = $this->workflowSteps[rand(0, 3)];

        if ($nextStep != 'open') {
            $workflowItem = $this->workflowManager->getWorkflowItemByEntity($issue);
            $this->workflowManager->transit($workflowItem, $nextStep);
            $stepName = $workflowItem->getCurrentStep()->getName();

            $issueResolutionRepoClass = ExtendHelper::buildEnumValueClassName('issue_resolution');
            $resolution = $this->em->getRepository($issueResolutionRepoClass)->find(Issue::RESOLUTION_UNRESOLVED);

            if (in_array($stepName, ['resolved', 'closed'])) {
                $resolution = $this->em->getRepository($issueResolutionRepoClass)->find(Issue::RESOLUTION_FIXED);
            }

            $issue->setIssueResolution($resolution);
        }
    }

    /**
     * @return \DateTime
     */
    protected function getRandomDate()
    {
        $result = new \DateTime('now', new \DateTimeZone('UTC'));
        $result->sub(new \DateInterval(sprintf('P%dDT%dM', rand(0, 30), rand(0, 1440))));

        return $result;
    }
}
