<?php

namespace Kuda\KudaEncyption\Facades;

use Illuminate\Support\Facades\Facade;

class KudaEncyption extends Facade {

    /**
    * Get the registered name of the component.
    *
    * @return string
    */
    protected static function getFacadeAccessor() { return 'kuda-encyption'; }

}