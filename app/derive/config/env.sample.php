<?php

return [
    'defaults' => [
        'domain' => '',
    ],
    'database' => [
        'default'    => [
            'user' => '',
            'pass' => '',
        ],
        'queue'      => [
            'user' => '',
            'pass' => '',
        ],
        'deriveprod' => [
            'host' => '',
            'user' => '',
            'pass' => '',
        ],
    ],
    'furs'     => [
        'env'                       => 'dev',
        'taxNumber'                 => '10450505',
        'pemCert'                   => '10450505-1.pem',
        'p12Cert'                   => '10450505-1.p12',
        'password'                  => 'FK9M8AMMS8HV',
        'serverCert'                => 'fursserver.pem',
        'url'                       => 'https://blagajne-test.fu.gov.si:9002/v1/cash_registers',
        'softwareSupplierTaxNumber' => '10450505',
        'businessId'                => 'GNPSI',
        'businessTaxNumber'         => '10450505',
        'businessValidityDate'      => '1990-08-25',
    ],
];