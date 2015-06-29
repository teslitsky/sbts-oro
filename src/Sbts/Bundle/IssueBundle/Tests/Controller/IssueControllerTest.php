<?php

namespace Sbts\Bundle\IssueBundle\Tests\Controller;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class IssueControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->getUrl('sbts_issue_index'));
        $response = $this->client->getResponse();
        $this->assertHtmlResponseStatusCodeEquals($response, 200);
    }
}
