<?php

require_once('../config.php');
require_once('../class/base.vestacp.php');

$vestacp_required_args = array(
    'hostname' => VESTACP_HOSTNAME,
    'user' => VESTACP_USERNAME,
    'password' => VESTACP_PASSWORD
);

try {
    $vestacp = new VestaCP($vestacp_required_args);
} catch (Exception $e) {
    die($e->getMessage());
}

$action_params = array(
    'json'
);

try {
    $results = $vestacp->action('v-list-sys-info', $action_params);
} catch (Exception $e) {
    die($e->getMessage());
}

echo '<pre>';
print_r($results);
echo '</pre>';
