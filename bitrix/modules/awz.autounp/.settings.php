<?php
return [
    'controllers' => [
        'value' => [
            'namespaces' => [
                '\\Awz\\AutoUnp\\Api\\Controller' => 'api'
            ]
        ],
        'readonly' => true
    ],
    'ui.entity-selector' => [
        'value' => [
            'entities' => [
                [
                    'entityId' => 'awzautounp-user',
                    'provider' => [
                        'moduleId' => 'awz.autounp',
                        'className' => '\\Awz\\Autounp\\Access\\EntitySelectors\\User'
                    ],
                ],
                [
                    'entityId' => 'awzautounp-group',
                    'provider' => [
                        'moduleId' => 'awz.autounp',
                        'className' => '\\Awz\\Autounp\\Access\\EntitySelectors\\Group'
                    ],
                ],
            ]
        ],
        'readonly' => true,
    ]
];