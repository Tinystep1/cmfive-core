<?php

class TaskAddActiveFlag extends CmfiveMigration {

	public function up() {
		// UP
		$this->addColumnToTable('task', 'is_active','boolean',['null' => true, 'default' => 1]);
	}

	public function down() {
		// DOWN
		$this->removeColumnFromTable('task', 'is_active');
	}

}
