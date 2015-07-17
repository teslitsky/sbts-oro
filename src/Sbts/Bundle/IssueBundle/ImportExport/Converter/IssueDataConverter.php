<?php

namespace Sbts\Bundle\IssueBundle\ImportExport\Converter;

use Oro\Bundle\ImportExportBundle\Converter\AbstractTableDataConverter;

class IssueDataConverter extends AbstractTableDataConverter
{
    /**
     * {@inheritdoc}
     */
    protected function getHeaderConversionRules()
    {
        return [
            'Organization' => 'organization:name',
            'Assignee'     => 'owner:username',
            'Reporter'     => 'reporter:username',
            'Type'         => 'issue_type',
            'Priority'     => 'issue_priority',
            'Resolution'   => 'issue_resolution',
            'Created'      => 'createdAt',
            'Updated'      => 'updatedAt',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getBackendHeader()
    {
        return [
            'id',
            'code',
            'issue_type',
            'summary',
            'description',
            'createdAt',
            'updatedAt',
            'reporter:username',
            'issue_priority',
            'issue_resolution',
            'collaborators',
            'organization:name',
            'owner:username',
        ];
    }
}

