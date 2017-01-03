<?php
/**
 * Project:     Merchant Access (MAC)
 * Team:        Rebel Alliance <rebel.alliance@nabancard.com>
 *
 * Created:     8/6/15, at 10:56 AM
 * @author      Yuxiao Tan <ytan@nabancard.com>
 * @copyright   1992-2016 North American Bancard
 */
namespace Rainflute\PackageTracker\Tracker;

/**
 * Class Tracker
 * @package Rainflute\PackageTracker\Tracker
 */
class Tracker extends BaseTracker implements TrackerInterface
{
    private $trackers = array();

//    /**
//     * Get Parameters for General Tracker
//     */
//    protected function getParameters()
//    {
//        $this->trackers[] = new UPSTracker();
//        $this->trackers[] = new USPSTracker();
//    }

    public function __construct($uspsConfig = [], $upsConfig = [])
    {
        $this->trackers[] = new UPSTracker($upsConfig);
        $this->trackers[] = new USPSTracker($uspsConfig);
    }

    /**
     * General Tracker
     *
     * @param $trackingNumber
     * @return array $response
     */
    public function track($trackingNumber)
    {
        $this->trackingNumber = $trackingNumber;

        foreach ($this->trackers as $tracker) {
            $response = $tracker->track($this->trackingNumber);
            if (isset($response['Carrier'])) { //If the tracking number is identified by a carrier
                break;
            } else {
                $response = [];
            }
        }

        return isset($response) ? $response : [];
    }
}
