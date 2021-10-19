<?php

namespace HexideDigital\AdminConfigurations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ConfigurationsFacade
 * @package HexideDigital\AdminConfigurations\Facades
 */
class ConfigurationsFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'admin_configurations';
    }
}
