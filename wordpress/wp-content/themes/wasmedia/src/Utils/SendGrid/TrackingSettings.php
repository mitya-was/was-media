<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/30/2017
 * Time: 13:16
 */

namespace Utils\SendGrid;


class TrackingSettings implements \jsonSerializable {
    private
        $click_tracking,
        $open_tracking,
        $subscription_tracking,
        $ganalytics;

    public function setClickTracking($click_tracking) {
        $this->click_tracking = $click_tracking;
    }

    public function getClickTracking() {
        return $this->click_tracking;
    }

    public function setOpenTracking($open_tracking) {
        $this->open_tracking = $open_tracking;
    }

    public function getOpenTracking() {
        return $this->open_tracking;
    }

    public function setSubscriptionTracking($subscription_tracking) {
        $this->subscription_tracking = $subscription_tracking;
    }

    public function getSubscriptionTracking() {
        return $this->subscription_tracking;
    }

    public function setGanalytics($ganalytics) {
        $this->ganalytics = $ganalytics;
    }

    public function getGanalytics() {
        return $this->ganalytics;
    }

    public function jsonSerialize() {
        return array_filter(
            [
                'click_tracking'        => $this->getClickTracking(),
                'open_tracking'         => $this->getOpenTracking(),
                'subscription_tracking' => $this->getSubscriptionTracking(),
                'ganalytics'            => $this->getGanalytics()
            ]
        );
    }
}