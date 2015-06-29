<?php

namespace Sbts\Bundle\IssueBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IssueType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'summary',
                'text',
                [
                    'required' => true,
                    'label'    => 'sbts.issue.label.summary',
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'required' => true,
                    'label'    => 'sbts.issue.label.description',
                ]
            )
            ->add(
                'issue_type',
                'oro_enum_select',
                [
                    'label'     => 'sbts.issue.label.type',
                    'enum_code' => 'issue_type',
                    'configs'   => [
                        'allowClear' => false,
                    ]
                ]
            )
            ->add(
                'issue_priority',
                'oro_enum_select',
                [
                    'label'     => 'sbts.issue.label.priority',
                    'enum_code' => 'issue_priority',
                    'configs'   => [
                        'allowClear' => false,
                    ]
                ]
            )
            ->add(
                'issue_resolution',
                'oro_enum_select',
                [
                    'label'     => 'sbts.issue.label.resolution',
                    'enum_code' => 'issue_resolution',
                    'configs'   => [
                        'allowClear' => false,
                    ]
                ]
            )
            ->add(
                'reporter',
                'oro_user_select',
                [
                    'required' => true,
                    'label'    => 'sbts.issue.label.reporter',
                ]
            )
            ->add(
                'assignee',
                'oro_user_select',
                [
                    'required' => true,
                    'label'    => 'sbts.issue.label.assignee',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Sbts\Bundle\IssueBundle\Entity\Issue']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sbts_issue';
    }
}
