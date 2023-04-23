<?php

declare(strict_types=1);

namespace App\Core\Validator;

use Exception;
use yii\web\HttpException;

class ValidateException extends HttpException
{
    public function __construct(
        private array $errors,
        string $message = 'Invalid validate.',
        int $code = 400,
        Exception $previous = null
    ) {
        parent::__construct($code, $message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
