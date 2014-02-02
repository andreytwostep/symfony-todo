<?php

namespace Dev\TaskBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaskType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('task', 'text', array(
				'label' => 'Task',
				'required' => true,
				'attr' => array(
					'placeholder' => 'What needs to be done?'
				)
			))
            ->add('complete', 'checkbox', array(
				'label' => 'Complete',
				'required' => false,
			));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dev\TaskBundle\Entity\Task'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dev_taskbundle_task';
    }
}
