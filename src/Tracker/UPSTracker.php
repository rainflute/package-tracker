<?php
/**
 * Project: Merchant Access (MAC)
 * Team: Rebel Alliance <rebel.alliance@nabancard.com>
 *
 * @copyright 1992-2016 North American Bancard
 *
 * Created by PhpStorm.
 * User: ytan
 * Date: 8/6/15
 * Time: 10:54 AM.
 */

namespace Rainflute\PackageTracker\Tracker;

/**
 * Set up the class for UPSTracker
 */
class UPSTracker extends BaseTracker implements TrackerInterface
{
    /** @var string The authentication key for API use */
    private $accessKey;

    /** @var string The user ID for API */
    private $userId;

    /** @var string The API password */
    private $pwd;

    /** @var string The API endpoint to request */
    private $endpointUrl;

    /** @var string WSDL file definition */
    private $wsdl;

    /** @var string Type of API operation */
    private $operation;

    /** @var string Which type of request */
    private $requestOption;

    /** @var string Custom description to send to API */
    private $customer_context = 'You customized description';

    /** @var string The tracking option type */
    private $trackingOption;

    /** @var string SOAP namespace */
    private $soap_header_namespace;

    protected $_config = [
        'accessKey' => null,
        'usps_api_url' => 'http://production.shippingapis.com/ShippingApi.dll',
        'userId' => null,
        'password' => null,
        'operation' => 'ProcessTrack',
        'requestOption' => '15',
        'trackingOption' => '02',
        'soapHeaderNamespace' => 'http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0',
        'wsdlPath' => __DIR__.'/../Resources/API/UPS/UPSTrack.wsdl',

    ];

    /**
     * UPSTracker constructor.
     * @param $config
     * @throws \Exception
     */
    public function __construct($config)
    {
        $valid = isset($config['username']) && isset($config['password']) && isset($config['accessKey']);
        if (!$valid) {
            throw new \Exception("'username', 'password', 'accessKey' are required, otherwise you cannot track ups shipment");
        }else{
            $this->setConfig($config);
        }
    }

    /**
     * Set the customer context
     *
     * @param string $text The context
     * @return void
     */
    public function setCustomerContext($text)
    {
        $this->customer_context = $text;
    }

    /**
     * Perform the tracking request, based on a tracking number
     *
     * @param string $tracking_number The UPS tracking number to request via the API
     * @return array The API response, encapsulated in an array with other keys
     */
    public function track($tracking_number)
    {
        $this->trackingNumber = $tracking_number;
        try {
            $mode = array(
                'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
                'trace' => 1,
            );

            // initialize soap client
            $client = new \SoapClient($this->wsdl, $mode);

            //set endpoint url
            $client->__setLocation($this->endpointUrl);

            //create soap header
            $usernameToken['Username'] = $this->userId;
            $usernameToken['Password'] = $this->pwd;
            $serviceAccessLicense['AccessLicenseNumber'] = $this->accessKey;
            $upss['UsernameToken'] = $usernameToken;
            $upss['ServiceAccessToken'] = $serviceAccessLicense;

            $header = new \SoapHeader($this->soap_header_namespace, 'UPSSecurity', $upss);
            $client->__setSoapHeaders($header);

            //get response
            $resp = $client->__soapCall($this->operation, array($this->getRequest()));
            $response = $resp->Shipment->Package->Activity;
            $response = array('TrackInfo' => json_decode(json_encode($response), true));
            $response['Carrier'] = 'UPS';

            if (array_key_exists('Status', $response['TrackInfo'])) {
                $tmp = $response['TrackInfo'];
                unset($response['TrackInfo']);
                $response['TrackInfo'][0] = $tmp;
            }

            return $this->format($response);
        } catch (\Exception $ex) {
            $error = $ex->detail->Errors->ErrorDetail->PrimaryErrorCode;
            $response = array('TrackError' => $ex->detail->Errors->ErrorDetail->PrimaryErrorCode);
            if ($error->Code == '151044') {
                //if the tracking number is a valid ups tracking number but information is not valid
                $response['Carrier'] = 'UPS';
            }

            return $response;
        }
    }

    /**
     * Format a Request array for the API to consume
     *
     * @return array The formatted request
     */
    private function getRequest()
    {
        $req['RequestOption'] = $this->requestOption;
        $tref['CustomerContext'] = $this->customer_context;
        $req['TransactionReference'] = $tref;
        $request['Request'] = $req;
        $request['InquiryNumber'] = $this->trackingNumber;
        $request['TrackingOption'] = $this->trackingOption;

        return $request;
    }
}
