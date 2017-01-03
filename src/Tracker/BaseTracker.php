<?php
/**
 * Project:     Merchant Access (MAC)
 * Team:        Rebel Alliance <rebel.alliance@nabancard.com>
 *
 * Created:     8/6/15, at 11:02 AM
 * @author      Yuxiao Tan <ytan@nabancard.com>
 * @copyright   1992-2016 North American Bancard
 */
namespace Rainflute\PackageTracker\Tracker;

/**
 * Class BaseTracker
 * @package Rainflute\PackageTracker\Tracker
 */
abstract class BaseTracker
{
    /**
     * @var string The tracking number used to track shipments */
    protected $trackingNumber = null;
    /**
     * @var string Format setting controls class response type */
    protected $format = 'array';

    protected $_config;

//    /**
//     * Gets Parameters for Base Tracker
//     */
//    abstract protected function getParameters();
//
//    /**
//     * BaseTracker constructor.
//     */
//    public function __construct( )
//    {
//        $this->getParameters();
//    }

    /**
     * Sets Format for Base Tracker
     *
     * @param $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Formats Base Tracker
     *
     * @param array $response
     * @return array|string
     */
    protected function format(array $response)
    {
        switch ($this->format) {
            case 'json':
                return json_encode($response);
                break;
            case 'array':
                return $response;
                break;
            //add other format
            default:
                return $response;
                break;
        }
    }

    /**
     * Set XML data to an Array
     * @param $xml
     * @param string $main_heading
     * @return mixed
     */
    protected function XMLToArray($xml, $main_heading = '')
    {
        $deXml = simplexml_load_string($xml);
        $deJson = json_encode($deXml);
        $xml_array = json_decode($deJson, true);
        if (!empty($main_heading)) {
            $returned = $xml_array[$main_heading];

            return $returned;
        } else {
            return $xml_array;
        }
    }

    /**
     * Sets specific config values (updates and keeps default values).
     *
     * @param array $config Params
     *
     * @return $this
     */
    public function setConfig(array $config)
    {
        foreach ($config as $key => $value) {
            $this->_config[$key] = $value;
        }

        return $this;
    }
}
