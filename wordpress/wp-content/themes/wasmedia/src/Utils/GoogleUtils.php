<?php
/**
 * Created by PhpStorm.
 * User: Oleksii Volkov (oleksijvolkov@gmail.com)
 * Date: 8/13/18
 * Time: 16:08
 */

namespace Utils;

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;
use Google_Client;

trait GoogleUtils {

    public static function googleAuthenticate() {
        $APP_NAME = "LOTTERY";
        $GOOGLE_CREDENTIALS = "client-secret.json";

        $prePath = dirname(__FILE__) . '/';
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $prePath . $GOOGLE_CREDENTIALS);

        $client = new Google_Client();

        $client->useApplicationDefaultCredentials();
        $client->setApplicationName($APP_NAME);
        $client->setScopes(['https://www.googleapis.com/auth/drive', 'https://spreadsheets.google.com/feeds']);

        if ($client->isAccessTokenExpired()) {
            $client->refreshTokenWithAssertion();
        }

        $accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];

        ServiceRequestFactory::setInstance(
            new DefaultServiceRequest($accessToken)
        );
    }
}