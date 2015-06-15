<?php

namespace Sbts\Bundle\IssueBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SbtsIssueBundle:Default:index.html.twig', array('name' => $name));
    }
}
