<?php

namespace Sbts\Bundle\IssueBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\EntityExtendBundle\Tools\ExtendHelper;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
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
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $issueTypeRepositoryClass = ExtendHelper::buildEnumValueClassName('issue_type');
        $issueTypeStory = $manager
            ->getRepository($issueTypeRepositoryClass)
            ->find(Issue::TYPE_STORY);

        $issueTypeSubTask = $manager
            ->getRepository($issueTypeRepositoryClass)
            ->findOneBy(Issue::TYPE_SUB_TASK);

        $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
        $reporter = $manager
            ->getRepository('OroUserBundle:User')
            ->findOneBy(['username' => 'admin']);

        $owner = $manager
            ->getRepository('OroUserBundle:User')
            ->findOneBy(['username' => 'manager']);

        if (!$issueTypeStory || !$issueTypeSubTask || !$organization || !$reporter || !$owner) {
            return;
        }

        foreach ($this->issues as $issue) {
            $entity = new Issue();
            $entity = $this->fillEntity($entity, $issue);
            $entity
                ->setReporter($reporter)
                ->setOwner($owner)
                ->setOrganization($organization)
                ->addCollaborator($reporter)
                ->addCollaborator($owner);

            if (isset($issue['subtasks'])) {
                foreach ($issue['subtasks'] as $data) {
                    $subTask = new Issue();
                    $subTask = $this->fillEntity($subTask, $data);
                    $subTask->setParent($entity);
                    $subTask
                        ->setReporter($reporter)
                        ->setOwner($owner)
                        ->setOrganization($organization)
                        ->addCollaborator($reporter)
                        ->addCollaborator($owner);

                    $manager->persist($subTask);
                    $manager->flush();

                    $this->setupIssueStatus($subTask);
                    $manager->flush();
                }

            }

            $manager->persist($entity);
            $manager->flush();

            $this->setupIssueStatus($entity);
            $manager->flush();
        }
    }

    /**
     * @param Issue $entity
     * @param array $data
     *
     * @return Issue
     */
    protected function fillEntity(Issue $entity, $data)
    {
        $entity
            ->setSummary($data['summary'])
            ->setDescription($data['description'])
            ->setIssueType($data['type'])
            ->setIssuePriority($data['priority'])
            ->setCreatedAt($this->getRandomDate())
            ->setUpdatedAt($this->getRandomDate());

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

            if (in_array($stepName, ['resolved', 'closed'])) {
                $issue->setIssueResolution(Issue::RESOLUTION_FIXED);
            }
        }
    }

    /**
     * @return string
     */
    protected function getRandomResolution()
    {
        return array_rand([
            Issue::RESOLUTION_FIXED,
            Issue::RESOLUTION_UNRESOLVED,
        ]);
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
