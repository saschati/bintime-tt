<?php

declare(strict_types=1);

namespace App\Core\Flusher;

use App\Core\Db\TransactionManager;
use Exception;
use RuntimeException;
use yii\db\ActiveRecordInterface;

class ActiveRecordManager implements EntityManagerInterface
{
    private const OPERATION_INSERT = 'insert';
    private const OPERATION_UPDATE = 'update';
    private const OPERATION_DELETE = 'delete';

    /**
     * @var ActiveRecordInterface[]
     */
    private array $insert = [];

    /**
     * @var ActiveRecordInterface[]
     */
    private array $update = [];

    /**
     * @var ActiveRecordInterface[]
     */
    private array $delete = [];

    public function __construct(private TransactionManager $transaction)
    {
    }

    public function persist(ActiveRecordInterface $record): void
    {
        ($record->getIsNewRecord() === true) ? $this->insert[] = $record : $this->update[] = $record;
    }

    public function remove(ActiveRecordInterface $record): void
    {
        if ($record->getIsNewRecord() === true) {
            return;
        }

        $this->delete[] = $record;
    }

    /**
     * @return ActiveRecordInterface[]
     */
    public function getInserts(): array
    {
        [$insert, $this->insert] = [$this->insert, []];

        return $insert;
    }

    /**
     * @return ActiveRecordInterface[]
     */
    public function getUpdates(): array
    {
        [$update, $this->update] = [$this->update, []];

        return $update;
    }

    /**
     * @return ActiveRecordInterface[]
     */
    public function getDeletes(): array
    {
        [$delete, $this->delete] = [$this->delete, []];

        return $delete;
    }

    /**
     * @throws Exception
     */
    public function flush(): void
    {
        $this->transaction->wrap(
            function (): void {
                $this->process(
                    $this->getInserts(),
                    self::OPERATION_INSERT,
                    'Insertion of records failed, rollback of the transaction.'
                );
                $this->process(
                    $this->getUpdates(),
                    self::OPERATION_UPDATE,
                    'Update records failed, transaction rolled back.'
                );
                $this->process(
                    $this->getDeletes(),
                    self::OPERATION_DELETE,
                    'The deletion of records was unsuccessful, the transaction was rolled back.'
                );
            }
        );
    }

    /**
     * @param ActiveRecordInterface[] $records
     *
     * @throws RuntimeException
     */
    private function process(array $records, string $operation, string $message): void
    {
        if ($records !== []) {
            foreach ($records as $record) {
                if ($record->{$operation}() === false) {
                    throw new RuntimeException($message);
                }
            }
        }
    }
}
