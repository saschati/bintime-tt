<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\Validator\Validator;
use App\ErrorHandler\ApiHandler;
use App\Http\Forms\CompositeForm;
use App\Http\Response\Transformer;
use InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Throwable;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\base\Model;
use yii\base\Module;
use yii\di\NotInstantiableException;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\HttpException;
use yii\web\Request;
use yii\web\Response;
use yii\web\User;

abstract class ApiController extends Controller
{
    protected User $user;
    protected Request $api;
    protected Validator $validator;
    protected Transformer $transformer;

    public function __construct(string $id, Module $module, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->user = app()->user;
        $this->api = app()->api;

        /** @var Validator validator */
        $this->validator = app(Validator::class);
        /** @var Transformer validator */
        $this->transformer = app(Transformer::class);
    }

    final public function behaviors(): array
    {
        return [
            [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    /**
     * @param mixed|string $id
     * @param array|mixed  $params
     *
     * @throws InvalidRouteException
     * @throws HttpException
     */
    final public function runAction($id, $params = []): mixed
    {
        try {
            return parent::runAction($id, $params);
        } catch (Throwable $exception) {
            return app(ApiHandler::class)->handler($exception);
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     */
    protected function load(string $class, string $name = null, array $params = [], array $data = []): Model
    {
        if (class_exists($class) === false) {
            throw new InvalidArgumentException("{$class} does not exist.");
        }

        if (is_subclass_of($class, Model::class) === false) {
            throw new InvalidArgumentException(sprintf('%s does not extends the class %s', $class, Model::class));
        }

        /** @var Model $form */
        $form = new $class(...$params);

        if ($form instanceof CompositeForm) {
            /** @var Serializer $serializer */
            $serializer = dic(Serializer::class);

            /** @var Model $form */
            $form = $serializer->denormalize(
                $data ?: $this->api->post($name),
                $class,
                'array',
                [
                    AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
                    AbstractNormalizer::ATTRIBUTES => $form->fields(),
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                    AbstractNormalizer::OBJECT_TO_POPULATE => $form,
                ]
            );

            return $form;
        }

        if ($form->load($data ?: $this->api->post(), $name) === false) {
            $form->addErrors(array_map(static fn () => 'Model is empty.', $form->fields()));
        }

        return $form;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function filter(string $class, array $params = []): Model
    {
        if (class_exists($class) === false) {
            throw new InvalidArgumentException("{$class} не існує");
        }

        if (is_subclass_of($class, Model::class) === false) {
            throw new InvalidArgumentException(sprintf('%s не наслідує клас %s', $class, Model::class));
        }

        /**
         * @var Model $filter
         */
        $filter = new $class(...$params);

        $filter->load($this->api->queryParams, '');

        return $filter;
    }

    protected function castCommand(Model $form, string $class, ?object $toPopulate = null, array $param = []): object
    {
        $serializer = app(Serializer::class);
        $options = array_merge([AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true], $param);

        if ($toPopulate !== null) {
            $options = array_merge([AbstractNormalizer::OBJECT_TO_POPULATE => $toPopulate], $options);
        }

        return $serializer->denormalize($form->getAttributes(), $class, 'array', $options);
    }

    protected function setStatus(int $status): void
    {
        $this->response->statusCode = $status;
    }

    protected function statusNotContent(): void
    {
        $this->setStatus(204);
    }

    protected function statusCreate(): void
    {
        $this->setStatus(201);
    }

    protected function statusOk(): void
    {
        $this->setStatus(200);
    }
}
