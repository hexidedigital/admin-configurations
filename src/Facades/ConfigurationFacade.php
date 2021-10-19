<?php

namespace HexideDigital\AdminConfigurations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ConfigurationFacade
 * @package HexideDigital\AdminConfigurations\Facades
 */
class ConfigurationFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'admin_configuration';
    }
}
