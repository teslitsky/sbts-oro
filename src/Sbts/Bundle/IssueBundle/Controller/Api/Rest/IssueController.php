<?php

namespace Sbts\Bundle\IssueBundle\Controller\Api\Rest;

use FOS\RestBundle\Controller\Annotations\NamePrefix;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Routing\ClassResourceInterface;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Symfony\Component\HttpFoundation\Response;

use Oro\Bundle\SecurityBundle\Annotation\Acl;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Oro\Bundle\SoapBundle\Controller\Api\Rest\RestController;
use Oro\Bundle\SoapBundle\Request\Parameters\Filter\HttpDateTimeParameterFilter;
use Oro\Bundle\SoapBundle\Request\Parameters\Filter\IdentifierToReferenceFilter;

/**
 * @RouteResource("issue")
 * @NamePrefix("sbts_api_")
 */
class IssueController extends RestController implements ClassResourceInterface
{
    /**
     * REST GET list
     *
     * @QueryParam(
     *      name="page",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Page number, starting from 1. Defaults to 1."
     * )
     * @QueryParam(
     *      name="limit",
     *      requirements="\d+",
     *      nullable=true,
     *      description="Number of items per page. Defaults to 10."
     * )
     * @QueryParam(
     *     name="createdAt",
     *     requirements="\d{4}(-\d{2}(-\d{2}([T ]\d{2}:\d{2}(:\d{2}(\.\d+)?)?(Z|([-+]\d{2}(:?\d{2})?))?)?)?)?",
     *     nullable=true,
     *     description="Date in RFC 3339 format. For example: 2009-11-05T13:15:30Z, 2008-07-01T22:35:17+08:00"
     * )
     * @QueryParam(
     *     name="updatedAt",
     *     requirements="\d{4}(-\d{2}(-\d{2}([T ]\d{2}:\d{2}(:\d{2}(\.\d+)?)?(Z|([-+]\d{2}(:?\d{2})?))?)?)?)?",
     *     nullable=true,
     *     description="Date in RFC 3339 format. For example: 2009-11-05T13:15:30Z, 2008-07-01T22:35:17+08:00"
     * )
     * @QueryParam(
     *     name="ownerId",
     *     requirements="\d+",
     *     nullable=true,
     *     description="Assignee owner Id"
     * )
     * @QueryParam(
     *     name="ownerUsername",
     *     requirements=".+",
     *     nullable=true,
     *     description="Assignee owner Username"
     * )
     * @ApiDoc(
     *      description="Gets all issues",
     *      resource=true
     * )
     * @AclAncestor("sbts_issue_view")
     *
     * @return Response
     */
    public function cgetAction()
    {
        $page = (int)$this->getRequest()->get('page', 1);
        $limit = (int)$this->getRequest()->get('limit', self::ITEMS_PER_PAGE);
        $dateParamFilter = new HttpDateTimeParameterFilter();

        $filterParameters = [
            'createdAt'     => $dateParamFilter,
            'updatedAt'     => $dateParamFilter,
            'ownerId'       => new IdentifierToReferenceFilter($this->getDoctrine(), 'OroUserBundle:User'),
            'ownerUsername' => new IdentifierToReferenceFilter($this->getDoctrine(), 'OroUserBundle:User', 'username'),
        ];

        $map = array_fill_keys(['ownerId', 'ownerUsername'], 'owner');
        $criteria = $this->getFilterCriteria(
            $this->getSupportedQueryParameters('cgetAction'),
            $filterParameters,
            $map
        );

        return $this->handleGetListRequest($page, $limit, $criteria);
    }

    /**
     * REST GET item
     *
     * @param string $id
     *
     * @ApiDoc(
     *      description="Gets issue",
     *      resource=true
     * )
     * @AclAncestor("sbts_issue_view")
     *
     * @return Response
     */
    public function getAction($id)
    {
        return $this->handleGetRequest($id);
    }

    /**
     * REST PUT
     *
     * @param int $id Issue item id
     *
     * @ApiDoc(
     *      description="Update issue",
     *      resource=true
     * )
     * @AclAncestor("sbts_issue_update")
     *
     * @return Response
     */
    public function putAction($id)
    {
        return $this->handleUpdateRequest($id);
    }

    /**
     * Create new issue
     *
     * @ApiDoc(
     *      description="Create new issue",
     *      resource=true
     * )
     * @AclAncestor("sbts_issue_create")
     *
     * @return Response
     */
    public function postAction()
    {
        return $this->handleCreateRequest();
    }

    /**
     * REST DELETE
     *
     * @param int $id
     *
     * @ApiDoc(
     *      description="Delete Issue",
     *      resource=true
     * )
     * @Acl(
     *      id="sbts_issue_delete",
     *      type="entity",
     *      class="SbtsIssueBundle:Issue",
     *      permission="DELETE"
     * )
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        return $this->handleDeleteRequest($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->get('sbts.manager.api');
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        return $this->get('sbts.form.api');
    }

    /**
     * {@inheritdoc}
     */
    public function getFormHandler()
    {
        return $this->get('sbts.form.handler.issue_api');
    }

    /**
     * {@inheritdoc}
     */
    protected function transformEntityField($field, &$value)
    {
        switch ($field) {
            case 'parent':
                if ($value) {
                    $value = $value->getCode();
                }

                break;

            case 'collaborators':
            case 'children':
            case 'related':
                $data = [];
                if (count($value)) {
                    foreach ($value as $item) {
                        $data[] = $item->getId();
                    }
                }

                $value = $data;
                break;

            case 'reporter':
            case 'owner':
            case 'workflowItem':
            case 'workflowStep':
                if ($value) {
                    $value = $value->getId();
                }

                break;

            default:
                parent::transformEntityField($field, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function fixFormData(array &$data, $entity)
    {
        parent::fixFormData($data, $entity);

        unset($data['id'], $data['createdAt'], $data['updatedAt']);

        return true;
    }
}
