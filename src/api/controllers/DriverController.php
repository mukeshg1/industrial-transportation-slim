<?php
/**
 * Manage truck details
 * created date:01/04/2019
 *
 * PHP version 5
 *
 * @author  Original Author <mukeshg@mindfiresolutions.com>
 * @version <GIT: mukeshg1/industrial-transportation-slim>
 */

namespace App\api\controllers;

use Slim\Http\UploadedFile;
use Interop\Container\ContainerInterface;
use App\api\models\DriverModel;
// use App\api\models\TruckDocumentModel;

require_once __DIR__ . '/../../constants/StatusCode.php';
require_once __DIR__ .'/../services/DecodeToken.php';

/**
 * Truck details insert and retreive
 *
 * Contain two property($container,$settings) one constructor
 * and
 */
class DriverController
{
    /**
     * Used to contain db instance
     *
     * @var Object
     */
    public $container;

    /**
     * Used to contain settings
     *
     * @var Object
     */
    public $settings;

    /**
     *  Initialize the FileMaker instance and get the settings
     *
     * @param object $container contain information related to db
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container->get('db');
        $this->settings = $container->get('settings');
    }

    /**
     * Upload driver details
     *
     * Rertrive the user id fom token. Take input and check for validate data,
     * return proper response in json format
     *
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     *
     * @return object return response object with JSON format
     */
    public function uploadDriverDetails($request, $response)
    {
        //get userID from token
        $id=decodeToken();
        //read input
        $body=$request->getParsedBody();

        $driverName = $body['driverName'];
        $driverLicenseNumber = $body['driverLicenseNumber'];
        $driverLicenseType = $body['driverLicenseType'];
        $licenseIssuedDate = $body['licenseIssuedDate'];
        $licenseExpiryDate = $body['licenseExpiryDate'];
        //if required inputs are emty then return an error with an error message
        if (empty($driverName) || empty($driverLicenseNumber) || empty($driverLicenseType) || empty($licenseIssuedDate) || empty($licenseExpiryDate)) {
            return $response->withJSON (
                ['error' => true, 'message' => 'Enter the required field.'], NOT_ACCEPTABLE );
        }
        /**
         * Used to store value of valid inputs
         *
         * @var array
         */
        $requestValue = array(
            "id"=>$id,
            "driverName" => $driverName,
            "driverLicenseNumber" => $driverLicenseNumber,
            "driverLicenseType" => $driverLicenseType,
            "licenseIssuedDate" => $licenseIssuedDate,
            "licenseExpiryDate" => $licenseExpiryDate
        );
        /**
         * Used to store instance of driverModel
         *
         * @var Object
         */
        $driverController = new DriverModel();
        $value = $driverController->uploadDriverDetails($requestValue, $this->container);
        print_r($value);
        exit();
        /**
         * If the return value of the function is string then return response with
         * corosponding message of the value
         */

        if (is_string($value)) {
            /**
             * Used to store responseMessage setting
             *
             * @var array
            */
            $errorMessage=$this->settings['responsMessage'];
            return $response->withJSON(
                ['error' => $errorMessage[$value]['error'],
                'message' => $errorMessage[$value]['message']],
                $errorMessage[$value]['statusCode']
            );
        }
    }

    /**
     * Fetch driver details
     *
     * @param object $request  represents the current HTTP request received
     *                         by the web server
     * @param object $response represents the current HTTP response to be
     *                         returned to the client.
     *
     * @return object return response object with JSON format
     */
    public function fetchDriverDetails($request, $response)
    {
        //get userID from token
        $id=decodeToken();
        $requestValue = array(
            "id"=>$id
        );
        /**
         * Used to store instance of DriverModel
         *
         * @var Object
         */
        $driverController=new DriverModel();
        $value=$driverController->fetchDriverDetails($requestValue, $this->container);
    
        if (is_string($value)) {
            /**
             * Used to store responseMessage setting
             *
             * @var array
            */
            $errorMessage=$this->settings['responsMessage'];
            return $response->withJSON(
                ['error' => $errorMessage[$value]['error'],
                'message' => $errorMessage[$value]['message']],
                $errorMessage[$value]['statusCode']
            );
        }
        return $response->withJSON(['error' => false, 'drivers' => $value], SUCCESS_RESPONSE);
    }
}