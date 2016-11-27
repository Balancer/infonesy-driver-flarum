<?php

use Phinx\Migration\AbstractMigration;

class Init extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('infonesy_map', ['id' => false, 'primary_key' => 'id']);
        $table
            ->addColumn('id', 'integer', ['signed' => false, 'identity' => true, 'limit' => 10])
            ->addColumn('uuid', 'string')
            ->addColumn('target_class_name', 'string')
            ->addColumn('target_id', 'integer', ['signed' => false, 'limit' => 10])
            ->addColumn('container_uuid', 'string', ['null' => true])
            ->addColumn('author_uuid', 'string', ['null' => true])

			->addIndex('uuid', ['unique' => true])
			->addIndex(['target_class_name', 'target_id'], ['unique' => true])
			->addIndex('container_uuid')
			->addIndex('author_uuid')

            ->create();
    }
}
