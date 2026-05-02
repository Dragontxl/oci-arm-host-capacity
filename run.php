<?php

require __DIR__ . '/vendor/autoload.php';

use Hitrov\OciApi;
use Hitrov\OciConfig;

$config = new OciConfig(
    getenv('OCI_REGION'),
    getenv('OCI_TENANCY_ID'),
    getenv('OCI_USER_ID'),
    getenv('OCI_KEY_FINGERPRINT'),
    getenv('OCI_PRIVATE_KEY_FILENAME')
);

$api = new OciApi();
$compartmentId = getenv('OCI_TENANCY_ID');
$shape = 'VM.Standard.A1.Flex'; // 直接写死，永远不会报错

try {
    $instances = $api->checkExistingInstances($config, $compartmentId, $shape);
    echo "Found existing instances: " . count($instances) . PHP_EOL;

    if (count($instances) >= getenv('OCI_MAX_INSTANCES')) {
        echo "Already have enough instances. Exiting." . PHP_EOL;
        exit(0);
    }
} catch (Exception $e) {
    echo "Error checking instances: " . $e->getMessage() . PHP_EOL;
}

echo "Trying to create instance..." . PHP_EOL;

try {
    $response = $api->createInstance(
        $config,
        $compartmentId,
        getenv('OCI_AVAILABILITY_DOMAIN'),
        getenv('OCI_IMAGE_ID'),
        getenv('OCI_SUBNET_ID'),
        getenv('OCI_OCPUS'),
        getenv('OCI_MEMORY'),
        $shape,
        getenv('OCI_SSH_PUBLIC_KEY')
    );
    echo "Created! Response: " . print_r($response, true);
} catch (Exception $e) {
    echo "Out of host capacity or error: " . $e->getMessage() . PHP_EOL;
}
