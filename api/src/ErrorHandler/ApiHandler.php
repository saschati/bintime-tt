<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use App\Core\Validator\ValidateException;
use App\Http\Response\Responder\Error\EntityResponder;
use App\Http\Response\Responder\Error\NotFoundResponder;
use App\Http\Response\Responder\Error\ViolationResponder;
use App\Http\Response\Transformer;
use App\Models\EntityException;
use Throwable;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ApiHandler
{
    private Response $response;

    public function __construct(
        private Transformer $transformer,
    ) {
        $this->response = app()->response;
    }

    /**
     * @throws Throwable
     */
    public function handler(Throwable $throwable)
    {
        $this->response->format = Response::FORMAT_JSON;

        if ($throwable instanceof ValidateException) {
            $this->response->setStatusCode(400);

            return $this->transformer->transform($throwable, new ViolationResponder());
        }

        if ($throwable instanceof NotFoundHttpException) {
            $this->response->setStatusCode(404);

            return $this->transformer->transform($throwable, new NotFoundResponder());
        }

        if ($throwable instanceof EntityException) {
            $this->response->setStatusCode(422);

            return $this->transformer->transform($throwable, new EntityResponder());
        }

        throw $throwable;
    }
}
