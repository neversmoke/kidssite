<?php

namespace Kids\SiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Itc\AdminBundle\Tools\LanguageHelper;
use Main\SiteBundle\Tools\ControllerHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Entity\User;
use Main\SiteBundle\Form\UserType;
use Main\SiteBundle\Form\UserSysType;
use Itc\KidsBundle\Entity\User\Adress;
use Kids\SiteBundle\Form\AdressType;
/**
 * @Route("/usercabinet", name="usercabinet_cons")
 */
class UsercabinetController extends ControllerHelper //Controller
{
    /**
     * @Route("/user/orders/{coulonpage}/{page}/{id}", name="user_orders",
     * requirements={"id" = "\d+", "coulonpage" = "\d+","page" = "\d+"}, 
     * defaults={ "id" = null, "coulonpage" = "10", "page"=1})
     * @Template()
     */
    public function ListOrdersAction($page, $id, $coulonpage = 10)
    {
        $sum=$products=$order=$lines="";
        $securityContext = $this->container->get('security.context');
         if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
        $user= $securityContext->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $qb = $em->getRepository('ItcDocumentsBundle:PdOrder\PdOrder')
                        ->createQueryBuilder('M')
                        ->select('M')
                        ->where('M.user=:user')
                        ->setParameter('user', $user);
                        $paginator = $this->get('knp_paginator');
                        
        $entities = $paginator->paginate(
                        $qb,
                        $this->get('request')->query->get('page', $page),
                        $coulonpage,
                        array('distinct' => false));
        
        if($id!=NULL){
             $order = $em->getRepository('ItcDocumentsBundle:PdOrder\PdOrder')->find($id);
             $lines = $order->getPdlines();
             foreach ($lines as $value) {
                 $products[$value->getId()]=$value->getProduct();
                 $sum=$sum+$value->getSumma1();
             }
        }
        return array( 
            'user'          => $user, 
            'pdorders'      => $entities,
            'order'         => $order,
            'lines'         => $lines,
            'product'       => $products,
            'total_price'   => $sum
        );         
       }
    }
    /**
     * @Route("/user/adresses", name="user_adresses")
     * @Template()
     */
    public function ListAdressesAction()
    {
        $securityContext = $this->container->get('security.context');
         if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
        $user= $securityContext->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $addreses =$em->getRepository('ItcKidsBundle:User\Adress')->findBy(array('userid'=>$user->getId()));
        return array( 
            'user'      => $user, 
            'adresses'  => $addreses,
        );         
                    
                }
        return array( 
            'user'      => "",
            'adresses'    => "",
        );
    }
    /**
     * @Route("/", name="usercabinet")
     * @Template()
     */
    public function indexAction()
    {
        $securityContext = $this->container->get('security.context');
         if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
        $user= $securityContext->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        
        $entities = $em->getRepository('ItcDocumentsBundle:PdOrder\PdOrder')->findBy(array('user'=>$user), array('date'=>'DESC'), 5 );
        
        $addreses =$em->getRepository('ItcKidsBundle:User\Adress')->findBy(array('userid'=>$user->getId()), array('street'=>'DESC'), 2 );
        return array( 
            'user'      => $user, 
            'pds'       => $entities,
            'adresses'  => $addreses,
        );         
                    
                }
        return array( 
            'user'      => "",
            'pds'        =>"",
            'adresses'    => "",
        );
    }

        /**
     * @Route("/usercabinet/info", name="usercabinet_info")
     * @Template()
     */
    public function PersonalInfoAction()
    {
        $securityContext = $this->container->get('security.context');
         if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
                    $user= $securityContext->getToken()->getUser();
                    $editForm = $this->createForm(new UserType(), $user, array("attr" => array("new" => false)));
                    $passForm = $this->createForm(new UserSysType(), $user, array("attr" => array("new" => false)));
                    $arr=array("user" => $user, 'entity' =>"", 'form' => $editForm->createView(),
                               'pass_form'=> $passForm->createView());
                    
                   
                    return $arr;
                
                    
                }
        return array( 
            'user' => "",
            'entity' =>"",
            'edit_form'   => "",
            'pass_form'   => ""
        );
    }
     /**
     * Edits an existing User entity.
     *
     * @Route("{id}/update", name="update_usercab")
     * @Method("POST")
     * @Template("")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $editForm = $this->createForm(new UserType(), $entity, array("attr" => array("new" => false)));
        $passForm = $this->createForm(new UserSysType(), $entity, array("attr" => array("new" => false)));
        $editForm->bind($request);
        $data = $editForm->getData();
        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            
            return $this->redirect($this->generateUrl('usercabinet_info'));
        }
        return array(
            'user'        => $entity,
            'entity'      => '',
            'pass_form'   => $passForm->createView(),
            'edit_form'   => $editForm->createView()
        );
    }
      /**
     * Edits an existing User entity.
     *
     * @Route("/{id}/usercab_pass", name="usercab_update_pass")
     * @Method("POST")
     * @Template("")
     */
    public function updatePassAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcAdminBundle:User')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $editForm = $this->createForm(new UserSysType(), $entity);
        $editForm1 = $this->createForm(new UserType(), $entity, array("attr" => array("new" => false)));
        
        $editForm->bind($request);
        $data = $editForm->getData();
        $passwd = $data->getPassword();
        
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $encodedPass = $encoder->encodePassword($passwd, $entity->getSalt());  
            $entity->setPassword($encodedPass);
            
        if ($editForm->isValid()) {
            $em->flush();
           return $this->redirect($this->generateUrl('usercabinet', array('id' => $id)));
        }
        
        return array(
            'user'        => $entity,
            'entity'      => '',
            'pass_form'   => $editForm->createView(),
            'edit_form'   => $editForm1->createView(),
        );
    }
      /**
     * Displays a form to create a new User entity.
     *
     * @Route("/registration", name="registration")
     * @Template()
     */
    public function registrationAction()
    {
        $entity = new User();
        $form   = $this->createForm(new UserType(), $entity, array("attr" => array("new" => true)));

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/registration/create", name="registrations_create")
     * @Method("POST")
     * @Template("ItcAdminBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        $group = $em->getRepository('ItcAdminBundle:Group')->findBy(array('role'=>'ROLE_USER'));
        foreach($group as $g)
        {
            $group=$g;
        }
        $entity = new User();
        $form = $this->createForm(new UserType(), $entity, array("attr" => array("new" => true)));
        $form->bind($request);
        $data = $form->getData();
        $passwd = $data->getPassword();
            $encoder = $this->get('security.encoder_factory')->getEncoder($entity);
            $encodedPass = $encoder->encodePassword($passwd, $entity->getSalt());  
            $entity->setPassword($encodedPass);
            $entity->setGroup($group);
            $entity->setEnabled(true);
            $entity->setFIO($data->getSurname()." ".$data->getName()." ".$data->getPatronymic());
        if ($form->isValid()) {
            
            $em->persist($entity);
            $em->flush();
            
            
            return $this->redirect($this->generateUrl('index'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }
    /**
     * @Route("/delete/addres/{id}", name="address_delete")
     * @Template("")
     */
       public function deleteAddrAction($id)
    {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ItcKidsBundle:User\Adress')->find($id);

            $em->remove($entity);
            $em->flush();

        return $this->redirect($this->generateUrl('user_adresses'));
    }
    /**
     * Displays a form to create a new User\Adress entity.
     *
     * @Route("/new", name="site_user_addr_new")
     * @Template()
     */
      public function newAddresAction()
    {
        $entity = new Adress();
        $form   = $this->createForm(new AdressType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * @Route("/adress/create/", name="site_user_addr_create")
     * @Template()
     */
    public function createAddresForUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->getTokenUser();
        $userid= $user!='' ? $user->getId() : ' ';
        
        
        
        $entity  = new Adress();
        $form = $this->createForm(new AdressType(), $entity);
        $form->bind($request);
        $entity->setUserid($userid);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('user_adresses'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User\Adress entity.
     *
     * @Route("/adress/{id}/edit", name="site_user_addr_edit")
     * @Template()
     */
    public function editAddressAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcKidsBundle:User\Adress')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User\Adress entity.');
        }

        $editForm = $this->createForm(new AdressType(), $entity);
        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView()
        );
    }

    /**
     * Edits an existing User\Adress entity.
     *
     * @Route("/{id}/update", name="site_user_addr_update")
     * @Template("ItcKidsBundle:User\Adress:edit.html.twig")
     */
    public function updateAddresAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ItcKidsBundle:User\Adress')->find($id);

        $editForm = $this->createForm(new AdressType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('site_user_addr_edit', array('id' => $id)));
        }

    }
   public function getTokenUser(){
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            return           $user= $securityContext->getToken()->getUser();
        }
        else{
            return $user="";
        }
    }
    
}
