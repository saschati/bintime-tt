<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Fetcher\User\TaskFetcher;
use App\Http\ApiController;
use App\Http\Forms\User\User\Task\CreateNewForm;
use App\Http\Forms\User\User\Task\EditForm;
use App\Http\Response\Responder\Collection\DataProviderResponder;
use App\Http\Response\Responder\Error\EntityResponder;
use App\Http\Response\Responder\Error\NotFoundResponder;
use App\Http\Response\Responder\Error\ViolationResponder;
use App\Http\Response\Responder\User\User\TaskResponder;
use App\Http\Response\Responder\User\User\TaskStatsResponder;
use App\Models\User\Entity;
use App\Models\User\UseCase\User\Task as TaskUseCase;
use App\OpenApi\User\TaskPaginateSchema;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use yii\base\Module;

class TaskController extends ApiController
{
    public function __construct(
        string $id,
        Module $module,
        private TaskFetcher $fetcher,
        private TaskUseCase\CreateNew\Handler $createNewHandler,
        private TaskUseCase\Edit\Handler $editHandler,
        private TaskUseCase\Remove\Handler $removeHandler,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    #[OA\Get(
        path: '/users/{id}/tasks',
        operationId: 'UserTaskAllTasks',
        description: 'Get the user\'s task list.',
        tags: ['User.Task'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid', example: '1da2e2c6-e904-4350-9c1e-d8fdcaaf7a99')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(description: 'Page Number', type: 'integer')),
            new OA\Parameter(name: 'sort', in: 'query', required: false, schema: new OA\Schema(type: 'string', enum: ['startedAt', '-startedAt', 'title', '-title', 'description', '-description'])),
        ],
    )]
    #[OA\Response(response: 200, description: 'Ok', content: new OA\JsonContent(ref: '#/components/schemas/' . TaskPaginateSchema::class))]
    public function actionIndex(string $id): array
    {
        $collection = $this->transformer->collection($this->fetcher->getAll($id), new TaskResponder());

        return $this->transformer->transform($collection, new DataProviderResponder());
    }

    #[OA\Get(
        path: '/users/{id}/tasks/stats',
        operationId: 'UserTaskAllStatsByUser',
        description: 'Get stats a task for the user.',
        tags: ['User.Task'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid', example: '1da2e2c6-e904-4350-9c1e-d8fdcaaf7a99')),
        ],
    )]
    #[OA\Response(response: 200, description: 'Ok', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/' . TaskStatsResponder::class)))]
    public function actionStatsByUser(string $id): array
    {
        $collection = $this->transformer->collection($this->fetcher->getStats($id), new TaskStatsResponder());

        return $collection->getModels();
    }

    #[OA\Get(
        path: '/tasks/stats',
        operationId: 'UserTaskAllStats',
        description: 'Get all stats for all users.',
        tags: ['User.Task'],
    )]
    #[OA\Response(response: 200, description: 'Ok', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/' . TaskStatsResponder::class)))]
    public function actionStats(): array
    {
        $collection = $this->transformer->collection($this->fetcher->getStats(), new TaskStatsResponder());

        return $collection->getModels();
    }

    #[OA\Post(
        path: '/users/{id}/tasks',
        operationId: 'UserTaskCreateNew',
        description: 'Create a new task for the user.',
        tags: ['User.Task']
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/' . CreateNewForm::class))]
    #[OA\Response(response: 201, description: 'Created', content: new OA\JsonContent(ref: '#/components/schemas/' . TaskResponder::class))]
    #[OA\Response(response: 400, description: 'Bad Request', content: new OA\JsonContent(ref: '#/components/schemas/' . ViolationResponder::class))]
    #[OA\Response(response: 404, description: 'Not Found', content: new OA\JsonContent(ref: '#/components/schemas/' . NotFoundResponder::class))]
    #[OA\Response(response: 422, description: 'Unprocessable Entity', content: new OA\JsonContent(ref: '#/components/schemas/' . EntityResponder::class))]
    public function actionStore(string $id): array
    {
        /** @var CreateNewForm $form */
        $form = $this->load(CreateNewForm::class, '');

        $this->validator->validate($form);

        $taskId = Entity\User\Task\Types\Id::new()->getValue();

        /** @var TaskUseCase\CreateNew\Command $command */
        $command = $this->castCommand(
            $form,
            TaskUseCase\CreateNew\Command::class,
            new TaskUseCase\CreateNew\Command($id, $taskId),
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['id', 'taskId']]
        );

        $this->createNewHandler->handle($command);

        $response = $this->transformer->transform($this->fetcher->get($id, $taskId), new TaskResponder());

        $this->statusCreate();

        return $response;
    }

    #[OA\Put(
        path: '/users/{id}/tasks/{taskId}',
        operationId: 'UserTaskEdit',
        description: 'Update task content for user.',
        tags: ['User.Task'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid', example: '1da2e2c6-e904-4350-9c1e-d8fdcaaf7a99')),
            new OA\Parameter(name: 'taskId', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid', example: '1da2e2c6-e904-4350-9c1e-d8fdcaaf7a99')),
        ]
    )]
    #[OA\RequestBody(content: new OA\JsonContent(ref: '#/components/schemas/' . EditForm::class))]
    #[OA\Response(response: 200, description: 'OK', content: new OA\JsonContent(ref: '#/components/schemas/' . TaskResponder::class))]
    #[OA\Response(response: 400, description: 'Bad Request', content: new OA\JsonContent(ref: '#/components/schemas/' . ViolationResponder::class))]
    #[OA\Response(response: 404, description: 'Not Found', content: new OA\JsonContent(ref: '#/components/schemas/' . NotFoundResponder::class))]
    #[OA\Response(response: 422, description: 'Unprocessable Entity', content: new OA\JsonContent(ref: '#/components/schemas/' . EntityResponder::class))]
    public function actionEdit(string $id, string $taskId): array
    {
        /** @var EditForm $form */
        $form = $this->load(EditForm::class, '');

        $this->validator->validate($form);

        /** @var TaskUseCase\Edit\Command $command */
        $command = $this->castCommand(
            $form,
            TaskUseCase\Edit\Command::class,
            new TaskUseCase\Edit\Command($id, $taskId),
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['id']]
        );

        $this->editHandler->handle($command);

        $response = $this->transformer->transform($this->fetcher->get($id, $taskId), new TaskResponder());

        $this->statusOk();

        return $response;
    }

    #[OA\Delete(
        path: '/users/{id}/tasks/{taskId}',
        operationId: 'UserTaskDelete',
        description: 'Delete a task for a user.',
        tags: ['User.Task'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid', example: '1da2e2c6-e904-4350-9c1e-d8fdcaaf7a99')),
            new OA\Parameter(name: 'taskId', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid', example: '1da2e2c6-e904-4350-9c1e-d8fdcaaf7a99')),
        ]
    )]
    #[OA\Response(response: 204, description: 'No Content', content: new OA\JsonContent())]
    #[OA\Response(response: 404, description: 'Not Found', content: new OA\JsonContent(ref: '#/components/schemas/' . NotFoundResponder::class))]
    #[OA\Response(response: 422, description: 'Unprocessable Entity', content: new OA\JsonContent(ref: '#/components/schemas/' . EntityResponder::class))]
    public function actionDelete(string $id, string $taskId): void
    {
        $command = new TaskUseCase\Remove\Command($id, $taskId);

        $this->removeHandler->handle($command);

        $this->statusNotContent();
    }
}
