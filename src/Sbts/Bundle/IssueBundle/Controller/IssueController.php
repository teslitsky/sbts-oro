<?php

namespace Sbts\Bundle\IssueBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;

use Sbts\Bundle\IssueBundle\Entity\Issue;

class IssueController extends Controller
{
    /**
     * @Route("/", name="sbts_issue_index")
     * @Acl(
     *      id="sbts_issue_view",
     *      type="entity",
     *      class="SbtsIssueBundle:Issue",
     *      permission="VIEW"
     * )
     * @Template
     */
    public function indexAction()
    {
        return ['entity_class' => $this->container->getParameter('sbts.issue.entity.class')];
    }

    /**
     * @Route("/create", name="sbts_issue_create")
     * @Acl(
     *      id="sbts_issue_create",
     *      type="entity",
     *      class="SbtsIssueBundle:Issue",
     *      permission="CREATE"
     * )
     * @Template("SbtsIssueBundle:Issue:update.html.twig")
     *
     * @return RedirectResponse
     */
    public function createAction()
    {
        $issue = new Issue();

        $issue->setReporter($this->getUser());

        $formAction = $this->get('oro_entity.routing_helper')
            ->generateUrlByRequest('sbts_issue_create', $this->getRequest());

        return $this->update($issue, $formAction);
    }

    /**
     * @Route("/view/{id}", name="sbts_issue_view", requirements={"id"="\d+"})
     * @AclAncestor("sbts_issue_view")
     * @Template
     *
     * @param Issue $issue
     *
     * @return array
     */
    public function viewAction(Issue $issue)
    {
        return ['entity' => $issue];
    }

    /**
     * @Route("/update/{id}", name="sbts_issue_update", requirements={"id"="\d+"})
     * @Acl(
     *      id="sbts_issue_update",
     *      type="entity",
     *      class="SbtsIssueBundle:Issue",
     *      permission="EDIT"
     * )
     * @Template
     *
     * @param Issue $issue
     *
     * @return array
     */
    public function updateAction(Issue $issue)
    {
        $formAction = $this->get('router')->generate('sbts_issue_update', ['id' => $issue->getId()]);

        return $this->update($issue, $formAction);
    }

    /**
     * @param Issue $issue
     * @param       $formAction
     *
     * @return array|RedirectResponse
     */
    protected function update(Issue $issue, $formAction)
    {
        if ($this->get('sbts_issue.form.handler.issue')->process($issue)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('sbts.issue.controller.message.saved')
            );

            return $this->get('oro_ui.router')->redirectAfterSave(
                [
                    'route'      => 'sbts_issue_update',
                    'parameters' => ['id' => $issue->getId()],
                ],
                [
                    'route'      => 'sbts_issue_view',
                    'parameters' => ['id' => $issue->getId()],
                ]
            );
        }

        return [
            'entity'     => $issue,
            'form'       => $this->get('sbts_issue.form.handler.issue')->getForm()->createView(),
            'formAction' => $formAction,
        ];
    }
}
