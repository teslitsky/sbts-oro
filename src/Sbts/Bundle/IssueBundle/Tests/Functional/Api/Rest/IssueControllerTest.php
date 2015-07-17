<?php

namespace Sbts\Bundle\IssueBundle\Tests\Functional\Api\Rest;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Sbts\Bundle\IssueBundle\Entity\Issue;

/**
 * @dbIsolation
 * @dbReindex
 */
class IssueControllerTest extends WebTestCase
{
    /**
     * @var array
     */
    protected $issue = [
        'summary' => 'Test issue',
        'description' => 'Test description',
    ];

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->initClient([], $this->generateWsseAuthHeader());
        $em = $this->getContainer()->get('doctrine')->getManager();

        if (!array_key_exists('owner', $this->issue)) {
            $this->issue['owner'] = $em
                ->getRepository('OroUserBundle:User')
                ->findOneBy(['username' => self::USER_NAME])
                ->getId();
        }

        if (!array_key_exists('issue_priority', $this->issue)) {
            $this->issue['issue_priority'] = Issue::PRIORITY_MAJOR;
        }

        if (!array_key_exists('issue_type', $this->issue)) {
            $this->issue['issue_type'] = Issue::TYPE_TASK;
        }
    }

    public function testCreate()
    {
        $this->client->request('POST', $this->getUrl('sbts_api_post_issue'), $this->issue);

        $issue = $this->getJsonResponseContent($this->client->getResponse(), 201);

        $this->assertArrayHasKey('id', $issue);
        $this->assertNotEmpty($issue['id']);

        return $issue['id'];
    }

    /**
     * @depends testCreate
     */
    public function testCget()
    {
        $this->client->request('GET', $this->getUrl('sbts_api_get_issues'));

        $issues = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertCount(1, $issues);
    }

    /**
     * @depends testCreate
     * @param int $id
     */
    public function testGet($id)
    {
        $this->client->request('GET', $this->getUrl('sbts_api_get_issue', ['id' => $id]));

        $issue = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($this->issue['summary'], $issue['summary']);

    }

    /**
     * @depends testCreate
     * @param int $id
     */
    public function testPut($id)
    {
        $updated = array_merge($this->issue, ['summary' => 'Updated summary']);

        $this->client->request(
            'PUT',
            $this->getUrl('sbts_api_put_issue', ['id' => $id]),
            $updated
        );

        $result = $this->client->getResponse();
        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('sbts_api_get_issue', ['id' => $id]));
        $issue = $this->getJsonResponseContent($this->client->getResponse(), 200);

        $this->assertEquals($updated['summary'], $issue['summary']);
    }

    /**
     * @depends testCreate
     * @param int $id
     */
    public function testDelete($id)
    {
        $this->client->request('DELETE', $this->getUrl('sbts_api_delete_issue', ['id' => $id]));
        $result = $this->client->getResponse();

        $this->assertEmptyResponseStatusCodeEquals($result, 204);

        $this->client->request('GET', $this->getUrl('sbts_api_get_issue', ['id' => $id]));
        $result = $this->client->getResponse();

        $this->assertJsonResponseStatusCodeEquals($result, 404);
    }
}
