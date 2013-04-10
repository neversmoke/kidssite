<?php


namespace Kids\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Itc\AdminBundle\Tools\LanguageHelper;
use Itc\AdminBundle\Tools\ControllerHelper;
use Main\SiteBundle\Controller\CartController;
use Kids\SiteBundle\Form\AdressType;
use Itc\KidsBundle\Entity\User\Adress;
use \Doctrine\Common\Collections\ArrayCollection;
use Main\SiteBundle\Form\SendMailType;
use Itc\DocumentsBundle\Entity\Pd\Trans;
use Itc\DocumentsBundle\Entity\Pd\Pd;
use Itc\DocumentsBundle\Entity\PdOrder\PdlList;
use Itc\DocumentsBundle\Entity\PdOrder\PdOrder;

/*
 * @Route("/checkouts")
 */
class CheckoutController extends ControllerHelper
{
    const PDTYPE = 1;
    const CART_USER = 'cart_user';
    const CART = 'order_ses';
    
    
   /**
    * @Route("/cart/updatecartmass" ,name="update_cart_mass")
    * @Template()
    */
    public function UpdateCartMassAction(Request $request)
    {
        $params=$request->request;
        
        $cart=$this->getCart();
        
        foreach ($params as $key => $value) {
            $cart[$key]=array(
                'id' => $cart[$key]['id'],
                'title' => $cart[$key]['title'],
                'price' => $cart[$key]['price'],
                'amount' => $value
            );
             $this->setCartSession( $cart );
        }
       return $this->redirect($this->generateUrl('cart')); 
    }

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
            
       $securityContext = $this->container->get('security.context');  
      if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
             $auth= 1;
       }else{ 
           $auth=0;
       }
        return array(
            'user'        => $user,
            'form'        => $editForm->createView(),
            'form1'       => $editForm->createView(),
            'address'     => $address,
            'auth'        => $auth
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
            $em->persist($entity);
            $em->flush();

            return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'form1'  => $form->createView(),
            'address'      => $address,
            'auth'         => 1
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
    * @Route("/checkout/draft/{id}" ,name="order_draft")
    * @Template()
    */
    public function DraftAction($id)
    {
        return array(
            'id' => $id,
        );
    }
   /**
    * @Route("/checkout/accept/{id}" ,name="checkout_method")
    * @Template()
    */
    public function CheckOutAction($id)
    {
        
        return $this->redirect($this->generateUrl('order_draft', array('id'=>$id)));
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
        
        $address_bill=$this->AdressBill('bill');
        $address_ship=$this->AdressBill('ship');
        
        if(is_object($address_bill)){
            $txt="<li>{$address_bill->getFio()}</li>
                    <li>{$address_bill->getStreet()}</li>
                    <li>{$address_bill->getHNum()}</li>
                    <li>{$address_bill->getPostcode()} {$address_bill->getCity()}</li>
                    <li>{$address_bill->getState()}</li>";
        }else{
             $txt="<li>{$address_bill['fio']}</li>
                    <li>{$address_bill['street']}</li>
                    <li>{$address_bill['h_num']}</li>
                    <li>{$address_bill['postcode']} {$address_bill['city']}</li>
                    <li>{$address_bill['state']}</li>";
        }
        
        if(is_object($address_ship)){
           $txt1="<li>{$address_ship->getFio()}</li>
                    <li>{$address_ship->getStreet()}</li>
                    <li>{$address_ship->getHNum()}</li>
                    <li>{$address_ship->getPostcode()} {$address_ship->getCity()}</li>
                    <li>{$address_ship->getState()}</li>";
        }else{
             $txt1="<li>{$address_ship['fio']}</li>
                    <li>{$address_ship['street']}</li>
                    <li>{$address_ship['h_num']}</li>
                    <li>{$address_ship['postcode']} {$address_ship['city']}</li>
                    <li>{$address_ship['state']}</li>";
        }
        
        
        $test = $this->acceptAction();
        
        $pd=$em->getRepository('ItcDocumentsBundle:Pd\Pd')->find($test);
        $pd->setTxt1($txt);
        $pd->setTxt2($txt1);
        $em->persist( $pd );
        $em->flush();
        
             $lines = $pd->getPdlines();
             foreach ($lines as $value) {
                 $products[$value->getId()]=$value->getProduct();
             }
        $payment = $em->getRepository('ItcKidsBundle:Payment\PaymentMethod')->find($order['payment_meth']['id_meth']);
        
        $shipp = $em->getRepository('ItcKidsBundle:Shipping\ShippingMethod')->find($order['shipp_meth']['id_meth']);
        
        
        $this->setOrderSession( NULL );
        
        return array(
            'lines'         => $lines,
            'product'       => $products,
            'total_price'   => $sum,
            'bill'          => $address_bill,
            'ship'          => $address_ship,
            'pay'           => $payment,
            'ship_meth'     => $shipp,
            'id'            => $test,
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
    

    
     public function acceptAction(){
        $order=$this->getCartSession();
        $em = $this->getDoctrine()->getManager();
        
        $shipp = $em->getRepository('ItcKidsBundle:Shipping\ShippingMethod')->find($order['shipp_meth']['id_meth']);
        $payment = $em->getRepository('ItcKidsBundle:Payment\PaymentMethod')->find($order['payment_meth']['id_meth']);
        $cart = $this->getCart();

        if( ! $cart ) return $this->redirectToCart();
        
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
             $user= $securityContext->getToken()->getUser();
        }
        $summa1 = $summa2 = 0;
        $pd = new PdOrder();
        $pdlines = new ArrayCollection();
       
        $pd->setPayment($payment);
        $pd->setShiping($shipp);
        $mainproducts="Пользователь {$user->getFIO()} c номером телефона {$user->getTel()} проживающий по адресу: {$user->getAddress()}";
        $mainproducts.="<table border='1'><tr><td>Товар</td><td>Цена за шт.</td><td>Количество</td><td>Итого</td></tr>";
        
        foreach( $cart as $key => $product ){
            
            
            $priceOne = $product['price'];
            $amount = $product['amount'];
            $price = $priceOne * $amount;
            $summa1 += $price;
            $summa2 += $amount;
            $mainproducts.="<tr><td>{$product['title']}</td><td>{$product['price']}</td><td>{$product['amount']}</td><td>".$product['amount']*$product['price']."</td></tr>";
            $pdline = new PdlList();
            $product = $em->getRepository('ItcAdminBundle:Product\Product')->find($product['id']);
            $pdline->setProduct($product);
            $pdline->setPd( $pd );
            $pdline->setSumma1( $price );
            $pdline->setSumma2( $amount );

            $pdlines->set( $key, $pdline );
        }
        $summa1=$summa1+$shipp->getPrice();
        $mainproducts.="<tr><td></td><td></td><td>{$summa2}</td><td>Общая сумма: {$summa1}</td></tr></table><br/> Поступил в: ".date( 'Y-m-d H:i:s' );
        $pd->setPdtypeId( self::PDTYPE );
        $pd->setN( 'cart' );
        $pd->setPdlines( $pdlines );
        $pd->setDate( date( "Y-m-d H:i:s" ) );
        $pd->setSumma1( $summa1 );
        $pd->setSumma2( $summa2 );
        $pd->setDtcor( date( "Y-m-d H:i:s" ) );
        
        if( is_object($user) ){
             
             $pd->setUser($user);
                    
             /*       $transaction= new Trans();
                    $transaction->setPd($pd);
                    $transaction->setSumma($summa1);
                    $transaction->setIL2($user->getId());
                    $transaction->setOL2($user->getId());
             $pd->addTransaction($transaction);*/
        }
        
        $em->persist( $pd );
        $em->flush();
        $email="lenkov.alex@itcompany.kiev.ua";
        $this->sendMailAction($mainproducts, $user, $user->getEmail(), $email);
        $this->sendMailAction($mainproducts, $user, $email, $user->getEmail());
        $this->setCartSession( NULL );
        return $pd->getId();
    }
    
    
        public function sendMailAction($body, $user, $from, $to){
         
        $sendMailType = new SendMailType( LanguageHelper::getLocale() );
        $body = $this->renderView( 'MainSiteBundle:Default:OrderMail.html.twig', 
                                array( 'text' => $body) );

            $message = \Swift_Message::newInstance()
                        ->setSubject( 'Новый заказ' )
                        ->setFrom( $from )
                        ->setTo( $to )
                        ->setBody( $body , 'text/html');
            $this->get( 'mailer' )->send( $message );

    }
    
    
    
    public function AdressBill($type){
       $em = $this->getDoctrine()->getManager(); 
       $order=$this->getCartSession();
         if(isset($order[$type]['id']))
            {
              $address_bill = $em->getRepository('ItcKidsBundle:User\Adress')->find($order[$type]['id']);
            }else
            { 
                $address_bill=$order[$type];    
            }
      return $address_bill;
    }
    
   private function getCartSession(){

            return $this->getRequest()->getSession()->get( self::CART );
    }
   private function getCart(){

        return $this->getRequest()->getSession()->get( self::CART_USER );
    }
   private function setCartSession( $newCart ){

        return $this->getRequest()->getSession()->set( self::CART_USER, $newCart );
    }
   private function setOrderSession( $newCart ){

        return $this->getRequest()->getSession()->set( self::CART, $newCart );
    }
}
