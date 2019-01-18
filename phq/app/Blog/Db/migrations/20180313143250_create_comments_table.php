<?php

namespace App\Blog\Db\migrations;

use Phinx\Migration\AbstractMigration;

class CreateCommentsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $mysqlTextLength = [];
        if ($this->adapter instanceof \Phinx\Db\Adapter\MysqlAdapter) {
            $mysqlTextLength = [
                'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG
            ];
        }

        $this->table('comments', ['id' => false, 'primary_key' => 'id'])
            ->addColumn('id', 'uuid')
            ->addColumn('post_id', 'integer')
            ->addColumn('content', 'text', $mysqlTextLength)
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime', ['null' => true])
            ->create();
    }
}
