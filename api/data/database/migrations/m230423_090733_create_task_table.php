<?php

declare(strict_types=1);

use App\Core\Db\Migration\AbstractMigration;
use yii\base\NotSupportedException;

class m230423_090733_create_task_table extends AbstractMigration
{
    /**
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        $this->createTable(
            '{{%user_tasks}}',
            [
                'id' => $this->pk($this->uuid()),
                'title' => $this->string(255)->notNull(),
                'description' => $this->text()->notNull(),
                'status' => $this->string(20)->notNull(),
                'user_id' => $this->fk($this->uuid()->notNull(), '{{%user_users}}', 'id'),
                'started_at' => $this->timestamp()->notNull(),
            ]
        );
    }

    public function safeDown(): void
    {
        $this->dropFk('user_id', '{{%user_tasks}}', '{{%user_users}}');

        $this->dropTable('{{%user_tasks}}');
    }
}
