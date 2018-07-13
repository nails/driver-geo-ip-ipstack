<?php

namespace Nails\GeoIp\Driver;

use Nails\Factory;
use Nails\GeoIp\Exception\GeoIpDriverException;
use Nails\GeoIp\Interfaces\Driver;

class IpStack implements Driver
{
    /**
     * The base url of the ipstack.com service.
     * @var string
     */
    const BASE_URL = 'http://api.ipstack.com';

    // --------------------------------------------------------------------------

    /**
     * The status code for a successful response from the API
     * @var int
     */
    const STATUS_OK = 200;

    // --------------------------------------------------------------------------

    /**
     * The API Key to use.
     * @var string
     */
    protected $sApiKey;

    // --------------------------------------------------------------------------

    /**
     * The HTTP client to use
     * @var object
     */
    protected $oHttpClient;

    // --------------------------------------------------------------------------

    /**
     * IpStack constructor.
     */
    public function __construct()
    {
        $this->oHttpClient = Factory::factory('HttpClient');
        $this->sApiKey     = appSetting('sAccessKey', 'nailsapp/driver-geo-ip-ipstack');

        if (empty($this->sApiKey)) {
            throw new GeoIpDriverException('An IPStack Access Key must be provided.');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * @param string $sIp The IP address to look up
     *
     * @return \Nails\GeoIp\Result\Ip
     * @deprecated
     */
    public function lookup($sIp)
    {
        $oIp = Factory::factory('Ip', 'nailsapp/module-geo-ip');

        $oIp->setIp($sIp);

        try {

            $oResponse = $this->oHttpClient->request(
                'GET',
                static::BASE_URL . '/' . $sIp . '?access_key=' . $this->sApiKey
            );

            if ($oResponse->getStatusCode() === static::STATUS_OK) {

                $oJson = json_decode($oResponse->getBody());

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
            }

        } catch (\Exception $e) {
            //  @log the exception somewhere
        }

        return $oIp;
    }
}
