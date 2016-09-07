<?php

/**
 * VestaCP API Wrapper 
 * @author     Allen Jenkins <support@scriptgain.com>
 * @version 1.0
 */
class VestaCP {

    /**
     * Constructor 
     * @param array $params  valid parameters  hostname, username, password
     */
    public function __construct($params = array()) {

        if (!in_array('curl', get_loaded_extensions())) {
            throw new Exception("Curl is required before you continue!");
        }

        $this->required_params = array('hostname', 'user', 'password');

        $this->params = $params;

        if (isset($this->required_params) && !empty($this->required_params)) {
            foreach ($this->required_params as $r) {
                if (!isset($this->params[$r]) || empty($this->params[$r])) {
                    throw new Exception("Required constructor variable {$r} not set.");
                }
            }
        }
    }

    /**
     * Action 
     * @param string $function The API function you want to run on the VestaCP server
     * @param array $action_params The parameters you want to pass to the API as args
     * @example array('value1', 'value2') This will turn into array('arg1' => 'value1', 'arg2' => 'value2') and passed to the API in that format
     * 
     * For a full list of available commands, ssh into the VestaCP server, list the files in /usr/local/vesta/bin
     */
    public function action($function = 'v-list-user', $action_params = array()) {

        $this->params['cmd'] = $function;

        if (!in_array('json', $action_params) || in_array('returncode', $action_params)) {
            $this->params['returncode'] = 'yes';
        }

        if (isset($action_params) && !empty($action_params)) {
            $c = 1;
            foreach ($action_params as $p) {
                $this->params += array(
                    'arg' . $c++ => $p
                );
            }
        }

        $returned_action = $this->perform_action();

        if ($returned_action !== NULL && !is_numeric($returned_action)) {
            $results = $this->parse_return_type($returned_action);
        } else {
            $results = $this->parse_return_type($this->get_return_code($returned_action));
        }

        return $results;
    }

    /**
     * Perform Action
     * perform the request to the VestaCP API with curl 
     */
    public function perform_action() {

        $postdata = trim(http_build_query($this->params));
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://' . $this->params['hostname'] . ':8083/api/');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        $result = curl_exec($curl);

        /*
         * Cleanly return an error for the user
         */
        if (stristr($result, 'Error:')) {
            return array('error' => $result);
        }

        $data = json_decode($result, true);

        /*
         *  If we cannot json_decode, just return the output from the API, as it's probably a return code.
         */
        if ($data === NULL) {
            return $result;
        }

        return $data;
    }

    /**
     * Get Return Code
     * @param int $return_code the return code
     * @returns the value, name, and comment of a specific return code value  
     */
    public function get_return_code($return_code) {
        /*
         * In order for us to get a valid return code, this is required
         */
        if ($return_code === NULL) {
            throw new Exception("The return code is required!");
        }
        /*
         * Grab the valid return code list
         */
        $result = $this->return_code_list();

        return $result[$return_code];
    }

    /*
     * Parse Return Type
     * Parse the data and return it as json, xml, object, or array
     */

    private function parse_return_type($data) {
        if (isset($this->params['returntype']) && $this->params['returntype'] == 'json') {
            return json_encode($data, true);
        } else if (isset($this->params['returntype']) && $this->params['returntype'] == 'object') {
            return (object) $data;
        } else if (isset($this->params['returntype']) && $this->params['returntype'] == 'xml') {
            $xml = new SimpleXMLElement('<VestaCP/>');
            $this->array_to_xml($xml, $data);
            return $xml->asXML();
        } else {
            return $data;
        }
    }

    /**
     * Return Code List 
     * returns all return codes in a list with value, name, and comment 
     * List of Return Codes from http://vestacp.com/docs/api/
     */
    private function return_code_list() {
        $list_return_codes = array(
            array(
                'value' => 0,
                'name' => 'OK',
                'comment' => 'Command has been successfuly performed'
            ),
            array(
                'value' => 1,
                'name' => 'E_ARGS',
                'comment' => 'Not enough arguments provided'
            ),
            array(
                'value' => 2,
                'name' => 'E_INVALID',
                'comment' => 'Object or argument is not valid'
            ),
            array(
                'value' => 3,
                'name' => 'E_NOTEXIST',
                'comment' => 'Object doesn\'t exist'
            ),
            array(
                'value' => 4,
                'name' => 'E_EXISTS',
                'comment' => 'Object already exists'
            ),
            array(
                'value' => 5,
                'name' => 'E_SUSPENDED',
                'comment' => 'Object is suspended'
            ),
            array(
                'value' => 6,
                'name' => 'E_UNSUSPENDED',
                'comment' => 'Object is already unsuspended'
            ),
            array(
                'value' => 7,
                'name' => 'E_INUSE',
                'comment' => 'Object can\'t be deleted because is used by the other object'
            ),
            array(
                'value' => 8,
                'name' => 'E_LIMIT',
                'comment' => 'Object cannot be created because of hosting package limits'
            ),
            array(
                'value' => 9,
                'name' => 'E_PASSWORD',
                'comment' => 'Wrong password'
            ),
            array(
                'value' => 10,
                'name' => 'E_FORBIDEN',
                'comment' => 'Object cannot be accessed be the user'
            ),
            array(
                'value' => 11,
                'name' => 'E_DISABLED',
                'comment' => 'Subsystem is disabled'
            ),
            array(
                'value' => 12,
                'name' => 'E_PARSING',
                'comment' => 'Configuration is broken'
            ),
            array(
                'value' => 13,
                'name' => 'E_DISK',
                'comment' => 'Not enough disk space to complete the action'
            ),
            array(
                'value' => 14,
                'name' => 'E_LA',
                'comment' => 'Server is to busy to complete the action'
            ),
            array(
                'value' => 15,
                'name' => 'E_CONNECT',
                'comment' => 'Connection failed. Host is unreachable'
            ),
            array(
                'value' => 16,
                'name' => 'E_FTP',
                'comment' => 'FTP server is not responding'
            ),
            array(
                'value' => 17,
                'name' => 'E_DB',
                'comment' => 'Database server is not responding'
            ),
            array(
                'value' => 18,
                'name' => 'E_RRD',
                'comment' => 'RRDtool failed to update the database'
            ),
            array(
                'value' => 19,
                'name' => 'E_UPDATE',
                'comment' => 'Update operation failed'
            ),
            array(
                'value' => 20,
                'name' => 'E_RESTART',
                'comment' => 'Service restart failed'
            )
        );

        $list = array();

        /*
         * We're going to re-key these by the value just in case.
         */
        if (isset($list_return_codes) && !empty($list_return_codes)) {
            foreach ($list_return_codes as $k => $v) {
                $list[$k] = $v;
            }
        }

        return $list;
    }

    /**
     * Array To XML
     * Convert the returned array to an XML object
     */
    function array_to_xml(SimpleXMLElement $object, array $data) {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $new_object = $object->addChild($key);
                $this->array_to_xml($new_object, $value);
            } else {
                $object->addChild($key, $value);
            }
        }
    }

}
