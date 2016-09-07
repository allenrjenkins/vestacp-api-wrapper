<?php

/**
 * The VestaCP config and base file
 */
require_once('config.php');
require_once('class/base.vestacp.php');

$vestacp_required_args = array(
    'hostname' => VESTACP_HOSTNAME,
    'user' => VESTACP_USERNAME,
    'password' => VESTACP_PASSWORD
);

$vestacp = new VestaCP($vestacp_required_args);

$action_params = array(
    'admin', 'json'
);

try {
    $results = $vestacp->action('v-list-user', $action_params);
} catch (Exception $e) {
    die($e->getMessage());
}

echo '<pre>';
print_r($results);
echo '</pre>';
