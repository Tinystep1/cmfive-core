<?php

function setActive_ALL(Web $w) {
	//get taskgroup
	$p = $w->pathMatch('id');
	$id = $p['id'];
	$taskgroup = $w->Task->getTaskgroup($id);
	if (!empty($taskgroup)) {
		// check permissions
		if ($taskgroup->canSetActive($w->Auth->user())) {
			$active = $w->request('active');
			
			if ($active != $taskgroup->is_active) {
				$response = $taskgroup->setActive($active);
				$w->msg($response, '/task-group/viewmembergroup/{$taskgroup->id}');
			}
			
		} else {
			$w->error("You do not have permissions to modify this taskgroup");
		}
	} else {
		$w->error("No taskgroup found for id");
	}
}