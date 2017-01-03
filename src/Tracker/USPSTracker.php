<?php
/**
 * Project:     Merchant Access (MAC)
 * Team:        Rebel Alliance <rebel.alliance@nabancard.com>
 *
 * Created:     8/6/15, at 10:55 AM
 * @author      Yuxiao Tan <ytan@nabancard.com>
 * @copyright   1992-2016 North American Bancard
 */namespace Rainflute\PackageTracker\Tracker;

/**
 * Class USPSTracker
 * @package NAB\Bundle\ShipmentBundle\ShipmentTracker
 */
class USPSTracker extends BaseTracker implements TrackerInterface
{
    /**
     * @var $uspsApiUserId Usps API User ID
     */
    private $uspsApiUserId;
    /**
     * @var $xmlFormat Xml Format
     */
    private $xmlFormat;
    /**
     * @var $apiUrl Api Url
     */
    private $apiUrl;
    /**
     * @var $apiVersion Api Version
     */
    private $apiVersion;

    protected $_config = [

    ];

    /**
     * Gets parameters for USPS Tracker
     */
    protected function getParameters()
    {
        $this->uspsApiUserId = $this->service_container->getParameter('usps_api_userid');
        $this->xmlFormat = $this->service_container->getParameter('usps_xml_format');
        $this->apiUrl = $this->service_container->getParameter('usps_api_url');
        $this->apiVersion = $this->service_container->getParameter('usps_api_version');
    }

    /**
     * Track for  USPS
     * @param $tracking_number
     * @return array|bool|string
     */
    public function track($tracking_number)
    {
        $this->trackingNumber = $tracking_number;
        $xml = sprintf($this->xmlFormat, $this->uspsApiUserId, $this->trackingNumber);

        $curl = curl_init($this->apiUrl);
        curl_setopt($curl, CURLOPT_POSTFIELDS, "API=$this->apiVersion"."&XML=$xml");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $response = $this->XMLToArray($response);
        if (isset($response['TrackInfo'])) {
            if (isset($response['TrackInfo']['Error'])) {
                return array('TrackError' => $response['TrackInfo']['Error']);
            } else {
                $response['Carrier'] = 'USPS';

                return $this->format($response);
            }
        } else {
            return false; //Request failed
        }
    }
}
