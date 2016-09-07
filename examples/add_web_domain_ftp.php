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
    'USER',
    'DOMAIN',
    'FTP_USER',
    'FTP_PASSWORD',
    '[FTP_PATH]'
);

try {
    $results = $vestacp->action('v-add-web-domain-ftp', $action_params);
} catch (Exception $e) {
    die($e->getMessage());
}

echo '<pre>';
print_r($results);
echo '</pre>';
