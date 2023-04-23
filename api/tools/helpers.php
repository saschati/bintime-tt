<?php

declare(strict_types=1);

use App\Models\User\Entity\User\User;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\di\NotInstantiableException;
use yii\helpers\Url;
use yii\rbac\ManagerInterface;
use yii\web\Request;
use yii\web\Response;
use yii\web\Session;
use yii\web\UrlManager;
use yii\web\User as WebUser;

function env(string $key, mixed $default = null): mixed
{
    $value = (getenv($key) ?? $_ENV[$key] ?? $_SERVER[$key]);

    if ($value === false) {
        return $default;
    }

    return match (mb_strtolower($value)) {
        'true', '(true)' => true,
        'false', '(false)' => false,
        'null', '(null)' => null,
        default => $value,
    };
}

/**
 * @throws NotInstantiableException
 * @throws InvalidConfigException
 */
function dic(string $class = null, array $params = [], array $config = []): ?object
{
    if ($class === null) {
        return Yii::$container;
    }

    return Yii::$container->get($class, $params, $config);
}

function alias(string $alias): string
{
    return Yii::getAlias(sprintf('@%s', ltrim($alias, '@')));
}

function router(): UrlManager
{
    return app()->urlManager;
}

function route(array|string $params, bool $scheme = true): string
{
    return router()->createAbsoluteUrl(Url::to($params), $scheme);
}

function redirect(string|array $url, int $statusCode = 302, bool $absolute = true): Response
{
    if ($absolute === true) {
        $url = router()->createAbsoluteUrl(Url::to($url));
    }

    return response()->redirect($url, $statusCode);
}

function app(string $class = null): object
{
    if ($class !== null) {
        return dic($class);
    }

    return Yii::$app;
}

function user(): WebUser
{
    return app()->user;
}

function auth(): User|null
{
    return user()->identity;
}

function can(string $permission, array $params = [], bool $allowCaching = true): bool
{
    return user()->can($permission, $params, $allowCaching);
}

function any(array $permissions, array $params = [], array $allowCachings = []): bool
{
    foreach ($permissions as $permission) {
        $param = (isset($params[$permission]) === true) ? $params[$permission] : [];
        $allowCaching = (isset($allowCachings[$permission]) === true) ? $allowCachings[$permission] : true;
        if (user()->can($permission, $param, $allowCaching)) {
            return true;
        }
    }

    return false;
}

function session(): Session
{
    return app()->session;
}

function request(): Request
{
    return app()->request;
}

function db(): Connection
{
    return app()->db;
}

function response(int $code = null, string $format = null): Response
{
    $response = app()->response;

    if ($code) {
        $response->setStatusCode($code);
    }

    if ($format) {
        $response->format = $format;
    }

    return $response;
}

function guard(): ManagerInterface
{
    return app()->authManager;
}

function __(string $category, string $message, array $params = [], string $language = null): string
{
    return Yii::t($category, $message, $params, $language);
}

function alert(string $key, string $message): void
{
    session()->setFlash($key, ['message' => $message]);
}
