<?php

declare(strict_types=1);

use yii\web\GroupUrlRule;

return [
    [
        'class'       => GroupUrlRule::class,
        'routePrefix' => 'Api/User',
        'rules'       => [
            [
                'class'       => GroupUrlRule::class,
                'prefix'      => 'api/users',
                'routePrefix' => 'Api/User',
                'rules'       => [
                    'GET '                => 'user/index',
                    'POST '               => 'user/store',
                    'PUT <id:[\w\-]+>'    => 'user/edit',
                    'DELETE <id:[\w\-]+>' => 'user/delete',
                    [
                        'class'       => GroupUrlRule::class,
                        'prefix'      => 'api/users',
                        'routePrefix' => 'Api/User',
                        'rules'       => [
                            'GET <id:[\w\-]+>/tasks'                     => 'task/index',
                            'GET <id:[\w\-]+>/tasks/stats'               => 'task/stats-by-user',
                            'POST <id:[\w\-]+>/tasks'                    => 'task/store',
                            'PUT <id:[\w\-]+>/tasks/<taskId:[\w\-]+>'    => 'task/edit',
                            'DELETE <id:[\w\-]+>/tasks/<taskId:[\w\-]+>' => 'task/delete',
                        ],
                    ],
                ],
            ],
            [
                'class'       => GroupUrlRule::class,
                'prefix'      => 'api/tasks',
                'routePrefix' => 'Api/User',
                'rules'       => [
                    'GET stats' => 'task/stats',
                ],
            ],
        ],
    ],
];
