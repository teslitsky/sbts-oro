<?php

namespace Sbts\Bundle\IssueBundle\Tests\Controller;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient();
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('sbts_issue_index'));
        $response = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($response, 200);

        $crawler = $this->client->request('GET', $this->getUrl('sbts_issue_index'));
        $this->assertTrue($crawler->filter('html:contains("Hello Fabien")')->count() === 1);
    }
}
