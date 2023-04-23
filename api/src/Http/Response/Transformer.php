<?php

declare(strict_types=1);

namespace App\Http\Response;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use yii\data\BaseDataProvider;

class Transformer
{
    public function __construct(private Serializer $serializer)
    {
    }

    public function transform($data, string|object $format): object|array
    {
        if (\is_object($format) === true && $format instanceof Responder) {
            return $format->transform($data);
        }

        $class = $format;
        $from = [];
        if (\is_object($format) === true) {
            $class = $format::class;
            $from[AbstractNormalizer::OBJECT_TO_POPULATE] = $format;
        }

        return $this->serializer->denormalize(
            $data,
            $class,
            'array',
            array_merge([AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true], $from)
        );
    }

    public function collection(array|BaseDataProvider $provider, string|object $format): array|BaseDataProvider
    {
        if (\is_object($format) === true && $format instanceof Responder) {
            $models = [];
            if (\is_array($provider) === true) {
                foreach ($provider as $model) {
                    $models[] = $format->transform($model);
                }

                return $models;
            }

            foreach ($provider->getModels() as $model) {
                $models[] = $format->transform($model);
            }

            $provider->setModels($models);

            return $provider;
        }

        $class = $format;
        $from = [];
        if (\is_object($format) === true) {
            $class = $format::class;
            $from[AbstractNormalizer::OBJECT_TO_POPULATE] = $format;
        }

        $provider->setModels(
            $this->serializer->denormalize(
                $provider->getModels(),
                sprintf('%s[]', $class),
                'array',
                array_merge([AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true], $from)
            )
        );

        return $provider;
    }
}
