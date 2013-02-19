<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kids\SiteBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\SecurityContext;
use FOS\UserBundle\Controller\SecurityController as SC;

class SecurityController extends SC
{
    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderLogin(array $data)
    {
        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            
                    $user= $securityContext->getToken()->getUser();
                    $arr=array("error"=> $data["error"],"last_username" => $user->getFIO(), "csrf_token"=>$data["csrf_token"]);
                    
                    $template = sprintf('KidsSiteBundle:Security:auth.html.%s', $this->container->getParameter('fos_user.template.engine'));

                    return $this->container->get('templating')->renderResponse($template, $arr);
                
                    
                }
        $template = sprintf('KidsSiteBundle:Security:login.html.%s', $this->container->getParameter('fos_user.template.engine'));

        return $this->container->get('templating')->renderResponse($template, $data);
    }
    
    
}
