<?php
/**
 * CRUD operation in file maker
 *
 * Genarelized function for CRUD operation. functions take LayoutName
 * and Field name and db instance as a input and perform operation
 * Created date : 29/03/2019
 *
 * @author  Original Author <wasifali591@gmail.com>
 * @version <GIT: wasifali591/industrial-transportation-slim>
 */

namespace App\api\services;

use Slim\Http\Response;


require_once __DIR__ .'/../../constants/StatusCode.php';

/**
 *
 */
class CRUDOperation
{
    /**
     * Create new record in db
     *
     * @param string $layoutName on which want to perform the opeartion
     * @param array  $fieldsName hold the field name
     * @param object $fm         database instance
     *
     * @return return multiple types of value according to the situation
     */
    public function createRecord($layoutName, $fieldsName, $fm)
    {
        $response=array();
        $fmquery = $fm->newAddCommand($layoutName);

        //For every key(Field Name) set their value
        while (list($key, $val) = each($fieldsName)) {
            $fmquery->setField($key, $val);
        }
        $result = $fmquery->execute();

        if ($fm::isError($result)) {
            return "SERVER_ERROR";
        }
        $recs = $result->getRecords();
        $record = $recs[0];
        $field=$record->getFields();
        foreach ($field as $field_name) {
            $response[$field_name] = $record->getField($field_name);
        }
        return $response;
    }

    /**
     * Find record into db
     *
     * @param string $layoutName on which want to perform the opeartion
     * @param array  $fieldsName hold the field name
     * @param object $fm         database instance
     *
     * @return multiple types of data return according to the situation
     */
    public function findRecord($layoutName, $fieldsName, $fm)
    {
        $count=count($fieldsName);
        $fmquery = $fm->newFindCommand($layoutName);

        if ($count===1) {
            $field=each($fieldsName);
            $fmquery->addFindCriterion($field['key'], '==' . $field['value']);
        } else {
            $fmquery->setLogicalOperator('FILEMAKER_FIND_AND');

            //while (list($key, $val) = each($fieldsName))
            foreach ($fieldsName as $key=>$val) {
                $fmquery->addFindCriterion($key, $val);
            }
        }
        $result = $fmquery->execute();
        
        if ($fm::isError($result)) {
            return false;
        }
        return true;
    }

    /**
     * Fetch record from db
     *
     * @param string $layoutName on which want to perform the opeartion
     * @param array  $fieldsName hold the id of the record with field name
     * @param object $fm         database instance
     *
     * @return multiple types of data return according to the situation
     */
    public function fetchRecord($layoutName, $fieldsName, $fm)
    {
        $response=array();
        $fmquery = $fm->newFindCommand($layoutName);
        $key=array_keys($fieldsName);
        $fmquery->addFindCriterion($key[0], $fieldsName[$key[0]]);
        $result = $fmquery->execute();
        
        if ($fm::isError($result)) {
            return "NOT_FOUND";
        }
        $recs = $result->getRecords();
        $count=0;
        foreach ($recs as $rec) {                    
            $field=$rec->getFields();
            foreach ($field as $field_name) {
                $res[$field_name] = $rec->getField($field_name);
            }
            $response[$count]=$res;
            $count++;          
        }
        return $response;
    }
}
