<?php

namespace Sbts\Bundle\IssueBundle\Tests\Functional\Controller;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testCreate()
    {
        $crawler = $this->client->request('GET', $this->getUrl('sbts_issue_create'));

        $form = $crawler->selectButton('Save and Close')->form();
        $form['sbts_issue[summary]'] = 'Test issue';
        $form['sbts_issue[description]'] = 'Test description';
        $form['sbts_issue[issue_type]'] = Issue::TYPE_BUG;
        $form['sbts_issue[issue_priority]'] = Issue::PRIORITY_MAJOR;
        $form['sbts_issue[reporter]'] = '1';
        $form['sbts_issue[assignee]'] = '1';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue has been saved', $crawler->html());
    }

    /**
     * @depends testCreate
     */
    public function testUpdate()
    {
        $response = $this->client->requestGrid(
            'issues-grid',
            ['issues-grid[_filter][summary][value]' => 'Test issue']
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $crawler = $this->client->request(
            'GET',
            $this->getUrl('sbts_issue_update', ['id' => $result['id']])
        );

        $form = $crawler->selectButton('Save and Close')->form();
        $form['sbts_issue[summary]'] = 'Issue updated summary';
        $form['sbts_issue[description]'] = 'Description updated';

        $this->client->followRedirects(true);
        $crawler = $this->client->submit($form);
        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue has been saved', $crawler->html());
    }

    /**
     * @depends testUpdate
     */
    public function testView()
    {
        $response = $this->client->requestGrid(
            'issues-grid',
            ['issues-grid[_filter][summary][value]' => 'Issue updated summary']
        );

        $result = $this->getJsonResponseContent($response, 200);
        $result = reset($result['data']);

        $this->client->request(
            'GET',
            $this->getUrl('sbts_issue_view', ['id' => $result['id']])
        );

        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issues', $result->getContent());
    }

    /**
     * @depends testUpdate
     */
    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('sbts_issue_index'));
        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);
        $this->assertContains('Issue updated summary', $result->getContent());
    }
}
