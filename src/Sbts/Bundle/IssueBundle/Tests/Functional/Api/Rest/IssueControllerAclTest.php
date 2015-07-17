<?php

namespace Sbts\Bundle\IssueBundle\Tests\Functional\Api\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Sbts\Bundle\IssueBundle\Entity\Issue;

/**
 * @dbIsolation
 */
class IssueControllerAclTest extends WebTestCase
{
    const USER_NAME = 'user_no_permissions';
    const USER_PASSWORD = 'user_api_key';

    /**
     * @var int
     */
    protected static $issueId;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->initClient([], $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD));
        $this->loadFixtures([
            'Sbts\Bundle\IssueBundle\Tests\Functional\Api\Rest\DataFixtures\LoadIssueData',
            'Sbts\Bundle\IssueBundle\Tests\Functional\Api\Rest\DataFixtures\LoadUserData',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function postFixtureLoad()
    {
        self::$issueId = $this
            ->getContainer()
            ->get('doctrine')
            ->getRepository('SbtsIssueBundle:Issue')
            ->findOneBy(['summary' => 'Test ACL issue'])
            ->getId();
    }

    public function testCreate()
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $user = $em
            ->getRepository('OroUserBundle:User')
            ->findOneBy(['username' => self::USER_NAME]);

        $request = [
            'summary'       => 'New issue',
            'description'   => 'New description',
            'issuePriority' => Issue::PRIORITY_MAJOR,
            'issueType'     => Issue::TYPE_STORY,
            'reporter'      => $user->getId(),
            'owner'         => $user->getId(),
        ];

        $this->client->request(
            'POST',
            $this->getUrl('sbts_api_post_issue'),
            $request,
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );

        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }

    /**
     * @depends testCreate
     */
    public function testCget()
    {
        $this->client->request(
            'GET',
            $this->getUrl('sbts_api_get_issues'),
            [],
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );

        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }

    /**
     * @depends testCreate
     */
    public function testGet()
    {
        $this->client->request(
            'GET',
            $this->getUrl('sbts_api_get_issue', ['id' => self::$issueId]),
            [],
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );

        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }

    /**
     * @depends testCreate
     */
    public function testPut()
    {
        $updatedTask = ['subject' => 'Updated summary'];

        $this->client->request(
            'PUT',
            $this->getUrl('sbts_api_put_issue', ['id' => self::$issueId]),
            $updatedTask,
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );

        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }

    /**
     * @depends testCreate
     */
    public function testDelete()
    {
        $this->client->request(
            'DELETE',
            $this->getUrl('sbts_api_delete_issue', ['id' => self::$issueId]),
            [],
            [],
            $this->generateWsseAuthHeader(self::USER_NAME, self::USER_PASSWORD)
        );

        $result = $this->client->getResponse();
        $this->assertJsonResponseStatusCodeEquals($result, 403);
    }
}
