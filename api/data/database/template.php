<?php declare(strict_types=1);
/**
 * This view is used by console/controllers/MigrateController.php.
 *
 * The following variables are available in this view:
 */

/**
 * @var string $namespace the new migration class namespace
 * @var string $className the new migration class name without namespace
 */
echo "<?php\n";
?>

use App\Core\Db\Migration\AbstractMigration;

class <?php echo $className; ?> extends AbstractMigration
{
    public function safeUp(): void
    {
        $this->createTable(
            '{{%table}}',
            [
                'id' => $this->pk($this->uuid()),
                'created_at' => $this->createdAtTimestamp(),
                'updated_at' => $this->updatedAtTimestamp(),
            ]
        );
    }

    public function safeDown(): void
    {
        $this->dropTable('{{%table}}');
    }
}
