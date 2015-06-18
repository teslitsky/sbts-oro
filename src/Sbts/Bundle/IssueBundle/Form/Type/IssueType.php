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
                'type',
                'entity',
                [
                    'required' => true,
                    'label'    => 'sbts.issue.label.type',
                    'class'    => 'Sbts\Bundle\IssueBundle\Entity\IssueType',
                ]
            )
            ->add(
                'priority',
                'entity',
                [
                    'required' => true,
                    'label'    => 'sbts.issue.label.priority',
                    'class'    => 'Sbts\Bundle\IssueBundle\Entity\IssuePriority',
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
