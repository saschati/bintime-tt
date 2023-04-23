<?php

declare(strict_types=1);

namespace App\Core\Validator;

use App\Http\Forms\CompositeForm;
use InvalidArgumentException;
use yii\base\Model;

class Validator
{
    /**
     * @throws ValidateException
     */
    public function validate(Model $form): void
    {
        $errors = $this->validateModel($form);

        if ($errors !== []) {
            throw new ValidateException($errors);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function validateModel(Model $form): array
    {
        $errors = [];
        if ($form->validate() === false) {
            $errors = $form->getErrors();
        }

        if ($form instanceof CompositeForm) {
            foreach ($form->forms() as $composite) {
                if ($form->{$composite} instanceof Model) {
                    $error = $this->validateModel($form->{$composite});
                    if ($error !== []) {
                        $errors[$composite] = $error;
                    }
                } elseif (\is_array($form->{$composite}) === true) {
                    foreach ($form->{$composite} as $item) {
                        $error = $this->validateModel($item);
                        if ($error !== []) {
                            $errors[$composite][] = $error;
                        }
                    }
                }
            }
        }

        return $errors;
    }
}
