<?php

namespace Kids\SiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class KidsSiteBundle extends Bundle
{
   public function getParent()
    {
        return 'MainSiteBundle';
    }
}
