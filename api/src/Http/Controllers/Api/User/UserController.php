<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Fetcher\User\UserFetcher;
use App\Http\ApiController;
use App\Http\Forms\User\User\EditProfileForm;
use App\Http\Forms\User\User\JoinByEmailForm;
use App\Http\Response\Responder\Collection\DataProviderResponder;
use App\Http\Response\Responder\Error\EntityResponder;
use App\Http\Response\Responder\Error\NotFoundResponder;
use App\Http\Response\Responder\Error\ViolationResponder;
use App\Http\Response\Responder\User\User\UserResponder;
use App\Models\User\Entity;
use App\Models\User\UseCase\User as UserUseCase;
use App\OpenApi\User\UserPaginateSchema;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use yii\base\Module;

class UserController extends ApiController
{
    public function __construct(
        string $id,
        Module $module,
        private UserFetcher $fetcher,
        private Entity\User\Repository\UserQueries $queries,
        private UserUseCase\JoinByEmail\Request\Handler $joinByEmailRequestHandler,
        private UserUseCase\EditProfile\Handler $editProfileHandler,
        private UserUseCase\DeleteProfile\Handler $deleteProfileHandler,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    #[OA\Get(
        path: '/users',
        operationId: 'UserAllUsers',
        description: 'Get a list of available users, broken down by pages and sorted.',
        tags: ['User'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(description: 'Page Number', type: 'integer')),
            new OA\Parameter(name: 'sort', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['registeredAt', '-registeredAt', 'firstName', '-firstName', 'lastName', '-lastName', 'email', '-email'])),
        ]
    )]
    #[OA\Response(response: 200, description: 'Ok', content: new OA\JsonContent(ref: '#/components/schemas/' . UserPaginateSchema::class))]
    public function actionIndex(): array
    {
        $collection = $this->transformer->collection($this->fetcher->getAll(), new UserResponder());

        return $this->transformer->transform($collection, new DataProviderResponder());
    }

    #[OA\Post(
        path: '/users',
        operationId: 'UserJoinByEmail',
        description: 'Create a new user by email.',
        tags: ['User']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/' . JoinByEmailForm::class))]
    #[OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/' . UserResponder::class))]
    #[OA\Response(response: 400, description: 'Bad Request', content: new OA\JsonContent(ref: '#/components/schemas/' . ViolationResponder::class))]
    #[OA\Response(response: 404, description: 'Not Found', content: new OA\JsonContent(ref: '#/components/schemas/' . NotFoundResponder::class))]
    #[OA\Response(response: 422, description: 'Unprocessable Entity', content: new OA\JsonContent(ref: '#/components/schemas/' . EntityResponder::class))]
    public function actionStore(): array
    {
        /** @var JoinByEmailForm $form */
        $form = $this->load(JoinByEmailForm::class);

        $this->validator->validate($form);

        $id = Entity\User\Types\Id::new()->getValue();

        /** @var UserUseCase\JoinByEmail\Request\Command $command */
        $command = $this->castCommand(
            $form,
            UserUseCase\JoinByEmail\Request\Command::class,
            new UserUseCase\JoinByEmail\Request\Command($id),
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['id']]
        );

        $this->joinByEmailRequestHandler->handle($command);

        $response = $this->transformer->transform($this->fetcher->get($id), new UserResponder());

        $this->statusCreate();

        return $response;
    }

    #[OA\Put(
        path: '/users/{id}',
        operationId: 'UserEditProfile',
        description: 'Update user by id.',
        tags: ['User'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid', example: '1da2e2c6-e904-4350-9c1e-d8fdcaaf7a99')),
        ]
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/' . EditProfileForm::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/' . UserResponder::class))]
    #[OA\Response(response: 400, description: 'Bad Request', content: new OA\JsonContent(ref: '#/components/schemas/' . ViolationResponder::class))]
    #[OA\Response(response: 404, description: 'Not Found', content: new OA\JsonContent(ref: '#/components/schemas/' . NotFoundResponder::class))]
    #[OA\Response(response: 422, description: 'Unprocessable Entity', content: new OA\JsonContent(ref: '#/components/schemas/' . EntityResponder::class))]
    public function actionEdit(string $id): array
    {
        /** @var Entity\User\User $entity */
        $entity = $this->queries->getById($id);
        /** @var EditProfileForm $form */
        $form = $this->load(EditProfileForm::class, params: [$entity]);

        $this->validator->validate($form);

        /** @var UserUseCase\EditProfile\Command $command */
        $command = $this->castCommand(
            $form,
            UserUseCase\EditProfile\Command::class,
            new UserUseCase\EditProfile\Command($id),
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['id']]
        );

        $this->editProfileHandler->handle($command);

        $response = $this->transformer->transform($this->fetcher->get($id), new UserResponder());

        $this->statusOk();

        return $response;
    }

    #[OA\Delete(
        path: '/users/{id}',
        operationId: 'UserDeleteProfile',
        description: 'Delete user by id.',
        tags: ['User'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid', example: '1da2e2c6-e904-4350-9c1e-d8fdcaaf7a99')),
        ]
    )]
    #[OA\Response(response: 204, description: 'No Content', content: new OA\JsonContent())]
    #[OA\Response(response: 404, description: 'Not Found', content: new OA\JsonContent(ref: '#/components/schemas/' . NotFoundResponder::class))]
    #[OA\Response(response: 422, description: 'Unprocessable Entity', content: new OA\JsonContent(ref: '#/components/schemas/' . EntityResponder::class))]
    public function actionDelete(string $id): void
    {
        $command = new UserUseCase\DeleteProfile\Command($id);

        $this->deleteProfileHandler->handle($command);

        $this->statusNotContent();
    }
}
