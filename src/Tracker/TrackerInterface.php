<?php
/**
 * Project:     Merchant Access (MAC)
 * Team:        Rebel Alliance <rebel.alliance@nabancard.com>
 *
 * Created:     8/6/15, at 11:12 AM
 * @author      Yuxiao Tan <ytan@nabancard.com>
 * @copyright   1992-2016 North American Bancard
 */
namespace Rainflute\PackageTracker\Tracker;

/**
 * Interface TrackerInterface
 * @package Rainflute\PackageTracker\Tracker
 */
interface TrackerInterface
{
    /**
     * Tracker interface for Tracker
     *
     * @param $tracking_number
     * @return array
     */
    public function track($tracking_number);

    /**
     * Sets format for Tracker interface
     *
     * @param $format
     * @return mixed
     */
    public function setFormat($format);
}
