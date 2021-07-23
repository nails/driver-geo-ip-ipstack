<?php

namespace Nails\GeoIp\Driver;

use Nails\Common\Driver\Base;
use Nails\Common\Service\HttpCodes;
use Nails\Factory;
use Nails\GeoIp;
use Nails\GeoIp\Exception\GeoIpDriverException;
use Nails\GeoIp\Interfaces\Driver;
use Nails\GeoIp\Result;

/**
 * Class IpStack
 *
 * @package Nails\GeoIp\Driver
 */
class IpStack extends Base implements Driver
{
    /**
     * The base url of the ipstack.com service.
     *
     * @var string
     */
    const BASE_URL = 'https://api.ipstack.com';

    // --------------------------------------------------------------------------

    /**
     * The API Key to use.
     *
     * @var string
     */
    protected $sAccessKey;

    // --------------------------------------------------------------------------

    /**
     * @param string $sIp The IP address to look up
     *
     * @return Result\Ip
     * @deprecated
     */
    public function lookup(string $sIp): Result\Ip
    {

        /** @var \Nails\Common\Factory\HttpRequest\Get $oHttpGet */
        $oHttpGet = Factory::factory('HttpRequestGet');
        /** @var \Nails\GeoIp\Result\Ip $oIp */
        $oIp = Factory::factory('Ip', GeoIp\Constants::MODULE_SLUG);
        $oIp->setIp($sIp);

        try {

            if (empty($this->sAccessKey)) {
                throw new GeoIpDriverException('An IPStack Access Key must be provided.');
            }

            $oResponse = $oHttpGet
                ->baseUri(static::BASE_URL)
                ->path($sIp)
                ->query([
                    'access_key' => $this->sAccessKey,
                ])
                ->execute();

            if ($oResponse->getStatusCode() !== HttpCodes::STATUS_OK) {
                $oResponseBody = $oResponse->getBody();
                throw new GeoIpDriverException(
                    $oResponseBody->error->info ?? HttpCodes::getByCode($oResponse->getStatusCode()),
                    $oResponseBody->error->code ?? $oResponse->getStatusCode()
                );
            }

            $oResponseBody = $oResponse->getBody();

            if (!empty($oResponseBody->error)) {
                throw new GeoIpDriverException(
                    $oResponseBody->error->info,
                    $oResponseBody->error->code
                );
            }

            if (!empty($oResponseBody->city)) {
                $oIp->setCity($oResponseBody->city);
            }

            if (!empty($oResponseBody->region_name)) {
                $oIp->setRegion($oResponseBody->region_name);
            }

            if (!empty($oResponseBody->country_name)) {
                $oIp->setCountry($oResponseBody->country_name);
            }

            if (!empty($oResponseBody->country_code)) {
                $oIp->setCountryCode($oResponseBody->country_code);
            }

            if (!empty($oResponseBody->continent_name)) {
                $oIp->setContinent($oResponseBody->continent_name);
            }

            if (!empty($oResponseBody->continent_code)) {
                $oIp->setContinentCode($oResponseBody->continent_code);
            }

            if (!empty($oResponseBody->latitude)) {
                $oIp->setLat($oResponseBody->latitude);
            }

            if (!empty($oResponseBody->longitude)) {
                $oIp->setLng($oResponseBody->longitude);
            }

        } catch (\Exception $e) {
            $oIp->setError($e->getMessage());
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return $oIp;
    }
}
