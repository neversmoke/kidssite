<?php

namespace Kids\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null, array( 'required'=>true,
                            'attr' => array(
                                'class' => 'input-text required-entry'
                            )))
            ->add('tel', null, array( 'required'=>true,
                            'attr' => array(
                                'class' => 'input-text required-entry'
                            )))
            ->add('surname', null, array( 'required'=>true,
                            'attr' => array(
                                'class' => 'input-text required-entry'
                            )))
            ->add('name', null, array( 'required'=>true,
                            'attr' => array(
                                'class' => 'input-text required-entry'
                            )))
            ->add('patronymic', null, array( 'required'=>true,
                            'attr' => array(
                                'class' => 'input-text required-entry'
                            )));
             if($options["attr"]["new"]){
                $builder->add('password', 'repeated', array(
                'type' => 'password',
                'first_options' => array('label' => 'password','attr' => array(
                                'class' => 'input-text required-entry'
                            )),
                'second_options' => array('label' => 'password_confirmation','attr' => array(
                                'class' => 'input-text required-entry'
                            )),
            ));}
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Itc\AdminBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'hoffice_sitebundle_usertype';
    }
}
