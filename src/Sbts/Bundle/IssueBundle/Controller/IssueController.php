<?php

namespace Sbts\Bundle\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IssueController extends Controller
{
    /**
     * @Route("/issue", name="sbts_issue_index")
     * @Template()
     */
    public function indexAction()
    {
        return [];
    }
}
