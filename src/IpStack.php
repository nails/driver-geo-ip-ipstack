<?php

namespace Nails\GeoIp\Driver;

use Nails\Common\Driver\Base;
use Nails\Factory;
use Nails\GeoIp\Exception\GeoIpDriverException;
use Nails\GeoIp\Interfaces\Driver;

class IpStack extends Base implements Driver
{
    /**
     * The base url of the ipstack.com service.
     * @var string
     */
    const BASE_URL = 'http://api.ipstack.com';

    // --------------------------------------------------------------------------

    /**
     * The API Key to use.
     * @var string
     */
    protected $sAccessKey;

    // --------------------------------------------------------------------------

    /**
     * @param string $sIp The IP address to look up
     *
     * @return \Nails\GeoIp\Result\Ip
     * @deprecated
     */
    public function lookup($sIp)
    {
        $oHttpClient = Factory::factory('HttpClient');
        $oIp         = Factory::factory('Ip', 'nailsapp/module-geo-ip');

        $oIp->setIp($sIp);

        try {

            if (empty($this->sAccessKey)) {
                throw new GeoIpDriverException('An IPStack Access Key must be provided.');
            }

            $oResponse = $oHttpClient->request(
                'GET',
                static::BASE_URL . '/' . $sIp . '?access_key=' . $this->sAccessKey . '2'
            );

            $oJson = json_decode($oResponse->getBody());

            if (empty($oJson->error)) {

                if (!empty($oJson->city)) {
                    $oIp->setCity($oJson->city);
                }

                if (!empty($oJson->region_name)) {
                    $oIp->setRegion($oJson->region_name);
                }

                if (!empty($oJson->country_name)) {
                    $oIp->setCountry($oJson->country_name);
                }

                if (!empty($oJson->latitude)) {
                    $oIp->setLat($oJson->latitude);
                }

                if (!empty($oJson->longitude)) {
                    $oIp->setLng($oJson->longitude);
                }
            } else {
                throw new GeoIpDriverException(
                    $oJson->error->info,
                    $oJson->error->code
                );
            }

        } catch (\Exception $e) {
            $oIp->setError($e->getMessage());
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return $oIp;
    }
}
