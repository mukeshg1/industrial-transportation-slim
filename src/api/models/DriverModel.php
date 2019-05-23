<?php
/**
 * Insert and retrieve data related to Truck
 * Created date : 01/04/2019
 *
 * PHP version 5
 * 
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */
namespace App\api\models;

use App\api\services\CRUDOperation;

/**
 * Contain
 */
class DriverModel
{
    private $_layoutName="DriverLayout";
    /**
     * Insert the truck details into the db
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function uploadDriverDetails($requestValue, $container)
    {
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $instance=new CRUDOperation();
        $fieldsName=array(
            "DrivingLicenceNumber_xt"=>$requestValue['driverLicenseNumber']
        );
        $result=$instance->findRecord($this->_layoutName, $fieldsName, $container);
        //if same truck is not present in the db then insert the input into db
        if (is_string($result)) {
            $fieldsName=array(
            "__kf_UserId_xn"=>$requestValue['id'],
            "DrivingLicenceType_xt"=>$requestValue['driverLicenseType'],
            "DrivingLicenceNumber_xt"=>$requestValue['driverLicenseNumber'],
            "DrivingLicenceIssueDate_xd"=>$requestValue['licenseIssuedDate'],
            "DrivingLicenceExpiryDate_xd"=>$requestValue['licenseExpiryDate'],
            "DriverName_xt"=>$requestValue['driverName']
            );
            $result=$instance->createRecord($this->_layoutName, $fieldsName, $container);
            /**
             * If the return value of the function is string then return response
             * with corosponding message of the value
             */
            if (is_string($result)) {
                return $result;
            }
        } else {
            return "DRIVER_ALREADY_REGISTERED";
        }
        return "SUCCESSFULLY_REGISTERED";
    }

    /**
     * Fetch truck details from the db
     *
     * @param array  $requestValue hold the value to be insert into db
     * @param object $container    hold the db instance
     *
     * @return multiple types of return according to the situation
     */
    public function fetchDriverDetails($requestValue, $container)
    {
        /**
         * Used to store instance of CRUDOperation
         *
         * @var object
         */
        $crud=new CRUDOperation();
        $fieldsName=array(
            "__kf_UserId_xn"=>$requestValue['id']
        );
        $result=$crud->findRecord($this->_layoutName, $fieldsName, $container);
        return $result;
    }
}
