<?php

namespace Main\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserSysType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('password', 'repeated', array(
                'type' => 'password',
                'first_options' => array('label' => 'password','attr' => array(
                                'class' => 'input-text required-entry'
                            )),
                'second_options' => array('label' => 'password_confirmation','attr' => array(
                                'class' => 'input-text required-entry'
                            )),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'hoffice_sitebundle_usersystype';
    }
}
