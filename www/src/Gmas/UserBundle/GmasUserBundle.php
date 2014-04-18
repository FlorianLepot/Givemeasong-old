<?php

namespace Gmas\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GmasUserBundle extends Bundle
{
  public function getParent() {
    return 'FOSUserBundle';
  }
}
