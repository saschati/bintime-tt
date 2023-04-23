<?php

declare(strict_types=1);

use App\Core\Db\Migration\AbstractMigration;
use yii\base\NotSupportedException;

class m230423_090308_create_user_table extends AbstractMigration
{
    /**
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        $this->createTable(
            '{{%user_users}}',
            [
                'id' => $this->pk($this->uuid()),
                'username' => $this->string(255)->unique()->notNull(),
                'email' => $this->string(255)->unique()->notNull(),
                'name' => $this->jsonb()->notNull(),
                'role' => $this->string(20)->notNull(),
                'status' => $this->string(20)->notNull(),
                'password_hash' => $this->string()->notNull(),
                'auth_key' => $this->string(32)->notNull(),
                'registered_at' => $this->createdAtTimestamp(),
            ]
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%user_users}}');
    }
}
