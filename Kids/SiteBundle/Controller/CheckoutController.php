<?php


namespace Kids\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Main\SiteBundle\Tools\ControllerHelper;
use Main\SiteBundle\Controller\CartController;
use Kids\SiteBundle\Form\AdressType;
use Itc\KidsBundle\Entity\User\Adress;
/*
 * @Route("/checkouts")
 */
class CheckoutController extends ControllerHelper
{
    const CART_USER = 'cart_user';
    const CART = 'order_ses';
   /**
    * @Route("/checkout/customer" ,name="customer")
    * @Template()
    */
    public function CustomerAction()
    {
        $user=$this->getTokenUser();
        $userid= $user!='' ? $user->getId() : ' ';
        
        $em = $this->getDoctrine()->getManager();
        
        $entity = new Adress();
            $editForm = $this->createForm(new AdressType(), $entity);
        
            $address = $em->getRepository('ItcKidsBundle:User\Adress')->findBy(array('userid'=>$userid));
        
        return array(
            'user'        => $user,
            'form'        => $editForm->createView(),
            'form1'       => $editForm->createView(),
            'address'     => $address
        );
    }
    
    /**
     * @Route("/checkout/customer/create_addr", name="user_addr_create_site")
     * @Template("KidsSiteBundle:Checkout:Customer.html.twig")
     */
    public function createAddressAction(Request $request)
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->getTokenUser();
        $userid= $user!='' ? $user->getId() : ' ';
        $address = $em->getRepository('ItcKidsBundle:User\Adress')->findBy(array('userid'=>$userid));
        $entity  = new Adress();
        
        $type=$request->request->get('types');
        
        $form = $this->createForm(new AdressType(), $entity);
        $form->bind($request);
        $entity->setUserid($userid);
        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'form1'  => $form->createView(),
            'address'      => $address
        );
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'form1'  => $form->createView(),
            'address'      => $address
        );
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
    
   /**
    * @Route("/checkout/shipping" ,name="shipp_method")
    * @Template()
    */
    public function ShippingAction()
    {
       $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ItcKidsBundle:Shipping\ShippingMethod')->findAll();

        return array(
            'entities' => $entities,
        );
    }
   /**
    * @Route("/checkout/payment" ,name="payment_method")
    * @Template()
    */
    public function PaymentAction()
    {
       $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('ItcKidsBundle:Payment\PaymentMethod')->findAll();

        return array(
            'entities' => $entities,
        );
    }
   /**
    * @Route("/checkout/accept" ,name="checkout_method")
    * @Template()
    */
    public function CheckOutAction()
    {
        
        $test = $this->get("checkout.handler")->acceptAction();
        return $this->redirect($this->generateUrl('index'));
    }
   /**
    * @Route("/checkout/review" ,name="review")
    * @Template()
    */
    public function ReviewAction()
    {
       $sum=0;
       $em = $this->getDoctrine()->getManager();
       
       $order=$this->getCartSession();
       $cart=$this->getCart();
       
       if($cart!=""){
        foreach($cart as $product){
            $sum=$sum+($product['price']*$product['amount']);
        }}
        
        if(isset($order['bill']['id']))
            {
              $address_bill = $em->getRepository('ItcKidsBundle:User\Adress')->find($order['bill']['id']);
            }else
            { 
                $address_bill=$order['bill'];    
            }
        
        if(isset($order['ship']['id']))
            {
              $address_ship = $em->getRepository('ItcKidsBundle:User\Adress')->find($order['ship']['id']);
            }else
            { 
                $address_ship=$order['ship'];    
            }
        
        $payment = $em->getRepository('ItcKidsBundle:Payment\PaymentMethod')->find($order['payment_meth']['id_meth']);
        
        $shipp = $em->getRepository('ItcKidsBundle:Shipping\ShippingMethod')->find($order['shipp_meth']['id_meth']);
        return array(
            'cart'          => $cart,
            'total_price'   => $sum,
            'bill'          => $address_bill,
            'ship'          => $address_ship,
            'pay'           => $payment,
            'ship_meth'     => $shipp,
        );
    }
   /**
    * @Route("/checkout/customer/wwww" ,name="user_add_session")
    * @Template()
    */
    public function AddSessionAction(Request $request)
    {
       
       $order=$this->getCartSession();
       
       $new=json_encode($request->request->get('session_arr'));
       $new=json_decode(json_decode($new, true),true); 
       foreach($new as $k=>$v){
         $order[$k]=$v;
       }
       
       $send=$request->request->get('send');
       
       $this->getRequest()->getSession()->set( self::CART,$order );
      return $this->redirect($this->generateUrl($send));
    }
    
   private function getCartSession(){

        return $this->getRequest()->getSession()->get( self::CART );
    }
   private function getCart(){

        return $this->getRequest()->getSession()->get( self::CART_USER );
    }
}
