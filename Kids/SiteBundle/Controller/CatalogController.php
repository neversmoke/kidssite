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
use Main\SiteBundle\Controller\CatalogController as Catal;
use Symfony\Component\Security\Core\SecurityContext;
use Kids\SiteBundle\Form\ProductCommentsType;
use Itc\AdminBundle\Entity\Comments\ProductComments;
/**
 * Catalog controller.
 * Routing registered in routing.yml
 */
class CatalogController extends ControllerHelper //Controller
{
    private $categories = array( 
        'ItcAdminBundle:Product\ProductGroup',
        'ItcAdminBundle:Product\ProductGroupTranslation'
    );
    private $menu = 'ItcAdminBundle:Menu\Menu';
    /**
     * @Template()
     */
    public function RatioBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.ratio is not null')
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();


        return array( 
            'entities'   => $entities,
            'locale'     => $locale,
        );
    }
    /**
     * @Template()
     */
    public function SliderBlockAction()
    {
        $images=array();
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('ItcAdminBundle:Banner\Banner')->findBy(array('is_used'=>TRUE));
        
        foreach ($entities as $gal) {
           $images[$gal->getId()]=$gal->getImg(); 
        }
        
        return array( 
            'entities'   => $entities,
            'images'     => $images,
        );
    }
     
    /**
     * @Route("catalog/{translit}/{sort}/{coulonpage}/{page}/", name="catalog",
     * requirements={"coulonpage" = "\d+","page" = "\d+"}, 
     * defaults={ "sort" = "kod", "coulonpage" = "9", "page"=1})
     * @Template()
     */
    public function CurrentCatalogAction($translit, $page, $sort = 'kod', $coulonpage = 9)
    {
      $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $entity = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.translit=:translit')
                        ->setParameter('translit', $translit)
                        ->setParameter('locale', $locale)
                        ->getQuery()->getOneOrNullResult();
        $ids[]=$entity->getId();
        $child=$entity->getChildren();
        if(is_object($child)){
            foreach ($child as $ch) {
                $ids[]=$ch->getId();
                $childs=$ch->getChildren();
                    foreach ($childs as $chs) {
                        $ids[]=$chs->getId();
                    }
            }
        }
        $entities = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.'.$sort, 'ASC')
                        ->where('M.productGroup in (:productGroup)')
                        ->setParameter('productGroup', $ids);
                        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
                        $entities,
                        $this->get('request')->query->get('page', $page)/*page number*/,
                        $coulonpage,
                        array('distinct' => false)
        );
        return array( 
            'entity'     => $entity,
            'locale'     => $locale,
            'id'         => $ids,
            'page'       => $page
        );
    }
    /**
     * @Template()
     */
    public function ProdBlockSortAction($id, $sort, $type, $page){
        $coulonpage=9;
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $entities = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->orderBy('M.'.$sort, $type)
                        ->where('M.productGroup in (:productGroup)')
                        ->setParameter('productGroup', $id);
                        $paginator = $this->get('knp_paginator');
        $entities = $paginator->paginate(
                        $entities,
                        $this->get('request')->query->get('page', $page)/*page number*/,
                        $coulonpage,
                        array('distinct' => false)
        );
        return array( 
            'entities'   => $entities,
        );
    }

    /**
     * @Template()
     */
    public function TopSalesBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.topSales=1')
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        $categories = $em->getRepository($this->menu)->findOneBy(array('routing'=>'categories'));

        return array( 
            'entities'   => $entities,
            'locale'     => $locale,
            'categories' => $categories,
        );
    } 
     /**
     * @Route("product/{translit}/", name="products")
     * @Template()
     */
    public function OneProductAction( $translit )
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        
        $entity = $em->getRepository('ItcAdminBundle:Product\Product')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->Where('M.translit=:translit')
                        ->setParameter('translit', $translit)
                        ->setParameter('locale', $locale)
                        ->getQuery()->getOneOrNullResult();
        
        $relative=$em->getRepository('ItcAdminBundle:Product\RelationsProdToProd')
                        ->createQueryBuilder('M')
                        ->select('M')
                        ->where('M.prod_id = :prod_id')
                        ->setParameter('prod_id', $entity->getId())
                        ->orderBy('M.kod', 'ASC')
                        ->getQuery()->execute();
        
        $relatives=$galary=$galary_images=array();
        
        foreach($relative as $rel){
            $relatives[]=$rel->getRelProd();
        }
        
        $galary=$em->getRepository('ItcAdminBundle:Product\ProductGalary')
                        ->createQueryBuilder('M')
                        ->select( 'M' )
                        ->Where('M.productId=:id')
                        ->setParameter('id', $entity->getId())
                        ->getQuery()->getOneOrNullResult();
        if(is_object($galary)){
        $galary_images=$em->getRepository('ItcAdminBundle:Product\ProductImage')
                        ->findByGallery($galary->getId());
        }
        $keywords=$entity->getKeywords();
         $massId=$ValueAttributes=$ProductAttributes="";
       
        $ProductAttributes=
                    $em->getRepository('ItcAdminBundle:Product\ProductAttribute')
                       ->createQueryBuilder('P')
                       ->select('P, R.name, R.id as resource, C.id as attrid')
                       ->InnerJoin('ItcAdminBundle:Template\AttributeResource', 
                               'R', 'WITH', 'P.attribute_resource_id=R.id')
                       ->InnerJoin('ItcAdminBundle:Template\Attribute', 
                               'C', 'WITH', 'P.attribute_resource_id=C.attribute_resource_id')
                       ->where("P.product = :id")
                       ->setParameter('id', $entity->getId())
                       ->groupBy('P.attribute_resource_id')
                       ->getQuery()
                       ->execute();
        if(count($ProductAttributes)>0){
            foreach ($ProductAttributes as $ProdAttr) {
              $massId.=$ProdAttr['resource'].",";
              
            }
        $ValueAttributes=
                    $em->getRepository('ItcAdminBundle:Product\ProductAttribute')
                       ->createQueryBuilder('P')
                       ->select('P, R.name, R.id as resource')
                       ->InnerJoin('ItcAdminBundle:Template\AttributeValueResource', 
                               'R', 'WITH', 'P.value_resource_id=R.id')
                       ->where("P.attribute_resource IN (".substr($massId, 0, -1).")")
                       ->AndWhere("P.product = :id")
                       ->setParameter('id', $entity->getId())
                       ->getQuery()
                       ->execute();
        
        }
        $securityContext = $this->container->get('security.context');
       if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
             $auth= 1;
       }else{ $auth=0; }
        $comment = new ProductComments();
        $form   = $this->createForm(new ProductCommentsType(), $comment);
        
        $comments = $em->getRepository('ItcAdminBundle:Comments\ProductComments')->createQueryBuilder('M')
                ->select('M, U.fio')
                ->innerJoin('M.autor', 'U')
                ->where('M.prod_id = :prod_id')
                ->setParameter('prod_id', $entity->getId())
                 ->orderBy('M.data', 'DESC')
                 ->getQuery()->execute();
        return array( 
            'entity'     => $entity,
            'relatives'  => $relatives,
            'images'     => $galary_images,
            'locale'     => $locale,
            'keywords'   => $keywords,
            'product_attr'  => $ProductAttributes,
            'attr_value'    => $ValueAttributes,
            'form'       => $form->createView(),
            'comments'   => $comments,
            'auth'       => $auth
        );
    }
    /**
     * Creates a new Comments\ProductComments entity.
     *
     * @Route("product/create/{id}", name="user_comments_create",
     * requirements={"id" = "\d+"})
     * @Template()
     */
    public function createAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity  = new ProductComments();
        
        $locale =  LanguageHelper::getLocale();
        $entity->setLang($locale);
        
        $product= $em->getRepository('ItcAdminBundle:Product\Product')->find($id);
        $entity->setProduct($product);
        $entity->setVisible(1);
        
        
        $Autor = $this->get('security.context')->getToken()->getUser();
        $entity->setAutor($Autor);
        
        $form = $this->createForm(new ProductCommentsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('products', array('translit' => $product->getTranslit())));
        }

    }
    /**
     *@Route("/producttag/{keyword}",  name="product_tag")
     *@Template()
     */
    public function ProductTagAction($keyword){
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $deflocale=LanguageHelper::getDefaultLocale();
        if($locale == $deflocale){
        $entity=$em->getRepository('ItcAdminBundle:Keyword\Keyword')
                       ->createQueryBuilder('M')
                        ->select('M')
                        ->where("M.translit = :translit ")
                        ->setParameter('translit', $keyword)
        ->getQuery()->getOneOrNullResult();    
        }else{
        $entity=$em->getRepository('ItcAdminBundle:Keyword\Keyword')
                       ->createQueryBuilder('M')
                        ->select('M, T')
                        ->leftJoin('M.translations', 'T',
                        'WITH', "T.locale = :locale")
                        ->setParameter('locale', $locale)
                        ->where("T.property='translit'")
        ->getQuery()->getOneOrNullResult();    
        }
        if (!$entity) {
            throw $this->createNotFoundException('The keyword does not exist');
        }     
        $entities=$entity->getProducts();
        return array( 'entity'   => $entity,
                       'entities' => $entities
                    );
    }
    /**
     * @Template()
     */
    
    public function CategoriesPageAction(){
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        return array( 
            'entities' => $entities,
            'locale' => $locale
        );
    }
    /**
     * @Template()
     */
    public function CatforSearchAction(){
        
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
                        ->createQueryBuilder('M')
                        ->select( 'M, T' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->orderBy('M.kod', 'ASC')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        return array( 
            'entities' => $entities,
            'locale' => $locale
        );
    }

    /**
     * @Template()
     */
    public function CategoriesBlockAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale =  LanguageHelper::getLocale();
        $queryBuilder = $em->getRepository('ItcAdminBundle:Product\ProductGroup')
                        ->createQueryBuilder('M')
                        ->select( 'M, T, count(P.id) as col' )
                        ->leftJoin('M.translations', 'T',
                                'WITH', "T.locale = :locale")
                        ->leftJoin('ItcAdminBundle:Product\Product', 
                               'P', 'WITH', 'P.productGroupId=M.id')
                        ->orderBy('M.kod', 'ASC')
                        ->groupBy('M.id')
                        ->setParameter('locale', $locale);

        $entities = $queryBuilder->getQuery()->execute();

        return array( 
            'entities' => $entities,
            'locale' => $locale
        );
    }
    /**
     * @Route("search/", name="search_in_site")
     * @Template()
     */
    public function SearchPageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $members = $qb = $em->getRepository( 'ItcAdminBundle:Product\Product' )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' );
        $qb->where( "M.title LIKE :value" );
        $qb->setParameter( 'value', "%".$_GET['q']."%" );

        $members = $qb->getQuery()->execute();
        if(count($members)==1){
             return $this->redirect($this->generateUrl('product', array('translit' =>  $members[0]->getTranslit())));
        }
        else{
             return array( 
            'entities'   => $members
            );
            
          }
    }
    /**
     * @Template()
     */
   public function SmallCartAction(){
       $col=0;
       $sum=0;
       $cart=$ids=$products="";
       $user=  $this->CurrentUser();
       if($this->getCartSession()!=''){
        $cart=$this->getCartSession();
       foreach($this->getCartSession() as $product){
           $ids[]=$product['id'];
           $col=$col+1;
           $sum=$sum+$product['price']*$product['amount'];
       }}
       if($ids!=''){
            $em = $this->getDoctrine()->getManager();
            $products = $em->getRepository('ItcAdminBundle:Product\Product')->findBy(
                array('id' => $ids));
        }
        return array( 
            'cart'          => $cart,
            'products'      => $products,
            'sum'           => $sum, 
            'col'           => $col,
            'user'          => $user);
    }
   private function getCartSession(){

        return $this->getRequest()->getSession()->get('cart_user');
    }
    /**
     * @Route("/auto/ajax_search_product.{_format}", name="ajax_search_products",
     * defaults={"_format" = "json"})
     */
    public function ajaxProductSearchAction(Request $request)
    {
        
        $value = $request->get('term');
        $em = $this->getDoctrine()->getEntityManager();
        $qb = $em->getRepository( 'ItcAdminBundle:Product\Product' )
                     ->createQueryBuilder( 'M' )
                     ->select( 'M' )
                    ->where( "M.title LIKE :value" )
                    ->setParameter( 'value', "%".$value."%" );
        if($request->get('parameter')){
                 $qb->andWhere("M.productGroup = {$request->get('parameter')}");
        }

        $members = $qb->getQuery()->execute();

        $json = array();
        foreach ($members as $member) {
           
                $json[] = array(
                'label' => $member->getArticle()." ".$member->getTitle(),
                'value' => $member->getId(),
                        );
            
        }

        $response = new Response();
        $response->setContent( json_encode( $json ) );

        return $response;
    }
    public function CurrentUser() {
      
       $securityContext = $this->container->get('security.context');
       if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
             $user= $securityContext->getToken()->getUser();
       }else{ $user=""; }
       return $user;
    }
}
