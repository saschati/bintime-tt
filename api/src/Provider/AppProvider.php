<?php

declare(strict_types=1);

namespace App\Provider;

use App\Core\Flusher\ActiveRecordManager;
use App\Core\Flusher\EntityManagerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer;
use Symfony\Component\Serializer\Serializer;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\db\Connection;
use yii\mail\MailerInterface;
use yii\rbac\ManagerInterface;

class AppProvider implements BootstrapInterface
{
    /**
     * @param Application|mixed $app
     */
    public function bootstrap($app): void
    {
        $container = Yii::$container;

        $container->setSingleton(ManagerInterface::class, static fn () => $app->authManager);
        $container->setSingleton(MailerInterface::class, static fn () => $app->mailer);
        $container->setSingleton(Connection::class, static fn () => $app->db);

        $container->setSingleton(EntityManagerInterface::class, ActiveRecordManager::class);

        $container->setSingleton(
            Serializer::class,
            static function () {
                $reflectionExtractor = new Extractor\ReflectionExtractor();
                $phpDocExtractor     = new Extractor\PhpDocExtractor();

                return new Serializer(
                    [
                        new Normalizer\ArrayDenormalizer(),
                        new Normalizer\ObjectNormalizer(
                            new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())),
                            null,
                            new PropertyAccessor(),
                            new PropertyInfoExtractor(
                                [$reflectionExtractor],
                                [
                                    $phpDocExtractor,
                                    $reflectionExtractor,
                                ],
                                [$phpDocExtractor],
                                [$reflectionExtractor],
                                [$reflectionExtractor]
                            )
                        ),
                        new Normalizer\JsonSerializableNormalizer(),
                        new Normalizer\PropertyNormalizer(),
                        new Normalizer\GetSetMethodNormalizer(),
                    ],
                    [
                        new Encoder\XmlEncoder(),
                        new Encoder\JsonEncoder(),
                    ]
                );
            }
        );
    }
}
