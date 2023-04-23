<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    version: '1.0.0',
    description: 'HTTP JSON API.',
    title: 'Bintime TT',
    x: [
    ]
)]
#[OAT\OpenApi(
    x: [
        'tagGroups' => [
            [
                'name' => 'User',
                'tags' => ['User', 'User.Task'],
            ],
        ],
    ]
)]
#[OAT\Server(
    url: API_V1_ENTRYPOINT,
    description: 'Api Bintime TT'
)]
#[OAT\PathItem(
    path: '/api',
    servers: [
    ]
)]
#[OAT\Tag(name: 'User', description: 'A list of methods for the user.')]
#[OAT\Tag(name: 'User.Task', description: 'List of methods for user tasks.')]
class OpenApi
{
}
