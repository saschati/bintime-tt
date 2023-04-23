<?php

declare(strict_types=1);

use App\Core\Db\Migration\AbstractMigration;
use yii\base\InvalidConfigException;
use yii\log\DbTarget;

class m230423_085852_log_init extends AbstractMigration
{
    /**
     * @var DbTarget[] Targets to create log table for
     */
    private $dbTargets = [];

    public function safeUp(): void
    {
        foreach ($this->getDbTargets() as $target) {
            $this->db = $target->db;

            $this->createTable(
                $target->logTable,
                [
                    'id' => $this->bigPrimaryKey(),
                    'level' => $this->integer(),
                    'category' => $this->string(),
                    'log_time' => $this->double(),
                    'prefix' => $this->text(),
                    'message' => $this->text(),
                ]
            );

            $this->createIndex('idx_log_level', $target->logTable, 'level');
            $this->createIndex('idx_log_category', $target->logTable, 'category');
        }
    }

    public function safeDown(): void
    {
        foreach ($this->getDbTargets() as $target) {
            $this->db = $target->db;

            $this->dropTable($target->logTable);
        }
    }

    /**
     * @return DbTarget[]
     * @throws InvalidConfigException
     */
    protected function getDbTargets()
    {
        if ($this->dbTargets === []) {
            $log = Yii::$app->getLog();

            $usedTargets = [];
            foreach ($log->targets as $target) {
                if ($target instanceof DbTarget) {
                    $currentTarget = [
                        $target->db,
                        $target->logTable,
                    ];
                    if (!in_array($currentTarget, $usedTargets, true)) {
                        // do not create same table twice
                        $usedTargets[] = $currentTarget;
                        $this->dbTargets[] = $target;
                    }
                }
            }

            if ($this->dbTargets === []) {
                throw new InvalidConfigException('You should configure "log" component to use one or more database targets before executing this migration.');
            }
        }

        return $this->dbTargets;
    }
}
