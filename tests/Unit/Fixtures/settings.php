<?php

declare(strict_types=1);

return [
    'SYS' => [
        'allowedPhpDisableFunctions' => [
            'set_time_limit',
            'putenv',
            'PHPINFO',
        ],
    ],
];
