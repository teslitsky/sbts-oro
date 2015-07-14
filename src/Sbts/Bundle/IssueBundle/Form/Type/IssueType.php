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
                    'label'    => 'sbts.issue.summary.label',
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'required' => true,
                    'label'    => 'sbts.issue.description.label',
                ]
            )
            ->add(
                'issue_type',
                'oro_enum_select',
                [
                    'label'     => 'sbts.issue.issue_type.label',
                    'enum_code' => 'issue_type',
                    'configs'   => [
                        'allowClear' => true,
                    ]
                ]
            )
            ->add(
                'issue_priority',
                'oro_enum_select',
                [
                    'label'     => 'sbts.issue.issue_priority.label',
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
                    'label'     => 'sbts.issue.issue_resolution.label',
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
                    'label'    => 'sbts.issue.reporter.label',
                ]
            )
            ->add(
                'owner',
                'oro_user_select',
                [
                    'required' => true,
                    'label'    => 'sbts.issue.assignee.label',
                ]
            )
            ->add(
                'tags',
                'oro_tag_select',
                [
                    'label' => 'oro.tag.entity_plural_label',
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => 'Sbts\Bundle\IssueBundle\Entity\Issue',
            'intention'          => 'issue',
            'cascade_validation' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sbts_issue';
    }
}
