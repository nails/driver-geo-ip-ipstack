<?php

namespace Nails\GeoIp\Driver\IpStack\Admin\Permission\Settings;

use Nails\Admin\Interfaces\Permission;

class IpStack implements Permission
{
    public function label(): string
    {
        return 'Can manage Geo-IP: IPStack settings';
    }

    public function group(): string
    {
        return 'Settings';
    }
}
