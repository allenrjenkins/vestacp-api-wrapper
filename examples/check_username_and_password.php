<?php

require_once('../config.php');
require_once('../class/base.vestacp.php');

$vestacp_required_args = array(
    'hostname' => VESTACP_HOSTNAME,
    'user' => VESTACP_USERNAME,
    'password' => VESTACP_PASSWORD
);

$vestacp = new VestaCP($vestacp_required_args);

$action_params = array(
    'demo', 'demodemo'
);

try {
    $results = $vestacp->action('v-check-user-password', $action_params);
} catch (Exception $e) {
    die($e->getMessage());
}

/*
 * Success will return the Return Code of 0 or OK
 * Failure will return the Return Code of 9 or E_PASSWORD
 */

echo '<pre>';
print_r($results);
echo '</pre>';
