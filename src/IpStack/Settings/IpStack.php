<?php

namespace Nails\GeoIp\Driver\IpStack\Settings;

use Nails\Common\Helper\Form;
use Nails\Common\Interfaces;
use Nails\Common\Service\FormValidation;
use Nails\Components\Setting;
use Nails\Factory;

/**
 * Class IpStack
 *
 * @package Nails\GeoIp\Driver\IpStack\Settings
 */
class IpStack implements Interfaces\Component\Settings
{
    const KEY_ACCESS_KEY = 'sAccessKey';

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getLabel(): string
    {
        return 'Geo-IP: IPInfo';
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function getPermissions(): array
    {
        return [];
    }

    // --------------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function get(): array
    {
        /** @var Setting $oAccessKey */
        $oAccessKey = Factory::factory('ComponentSetting');
        $oAccessKey
            ->setKey(static::KEY_ACCESS_KEY)
            ->setType(Form::FIELD_PASSWORD)
            ->setLabel('Access Key')
            ->setEncrypted(true)
            ->setValidation([
                FormValidation::RULE_REQUIRED,
            ]);

        return [
            $oAccessKey,
        ];
    }
}
