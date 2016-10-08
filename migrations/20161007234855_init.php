<?php

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
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
		$table = $this->table('users');
		$table
			// Internal (non-Infonesy, non-Flarum) user id. Created automatic!
			// ->addColumn('id', 'integer')
			// Infonesy UUID
			->addColumn('infonesy_uuid', 'string', ['length' => 255])
			// Flarum user id
			->addColumn('flarum_user_id', 'integer')
			->addColumn('email_md5', 'string', ['length' => 255])
			->create();

		$table = $this->table('posts');
		$table
			// Infonesy UUID
			->addColumn('infonesy_uuid', 'string', ['length' => 255])
			// Flarum post id
			->addColumn('flarum_post_id', 'integer')
			->create();
    }
}
