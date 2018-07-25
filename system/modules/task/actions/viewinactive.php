<?php

function viewinactive_ALL (Web $w) {
	$p = $w->pathMatch("id");
    $task = $w->Task->getTask($p["id"]);
    
    if (empty($task) || $task->is_deleted == 1) {
        $w->error('Task not found',"/task/tasklist/");
    }
    
    if (!empty($task->id) && !$task->canView($w->Auth->user())) {
        $w->error("You do not have permission to edit this Task", "/task/tasklist");
    }
    
    $tasktypes = array();
    $priority = array();
    $members = array();
    
    // Try and prefetch the taskgroup by given id
    $taskgroup = null;
    $taskgroup_id = $w->request("gid");
    $assigned = 0;
    if (!empty($taskgroup_id) || !empty($task->task_group_id)) {
        $taskgroup = $w->Task->getTaskGroup(!empty($task->task_group_id) ? $task->task_group_id : $taskgroup_id);
        
        if (!empty($taskgroup->id)) {
            $tasktypes = $w->Task->getTaskTypes($taskgroup->task_group_type);
            $priority = $w->Task->getTaskPriority($taskgroup->task_group_type);
            $members = $w->Task->getMembersBeAssigned($taskgroup->id);
            sort($members);
            array_unshift($members,array("Unassigned","unassigned"));
            $assigned = (empty($task->assignee_id)) ? "unassigned" : $task->assignee_id;
        }
    }

    //chect if task active and taskgroup
    if (!empty($task->id) && !$task->is_active) {
        if ($taskgroup->is_active) {
            $w->ctx('error','This Task is INACTIVE. No changes will be saved.');
        } else  {
            $w->ctx('error','This Task is INACTIVE. This Taskgroup is INACTIVE. No changes will be saved.');
        }
    }
    
    // Create form
    $form = array(
        'Task Details' => array(
            array(
            	array("Task Group", "static", "task_group", $taskgroup->getSelectOptionTitle()),
                array("Task Type", "static", "task_type", $task->task_type),
            ),
            array(
                array("Task Title", "static", "title", $task->title)
            ),
            array(
                array("Status", "static", "status", $task->status, $task->getTaskGroupStatus()),
                array("Active", "static", "set_is_active", $task->is_active ? "Yes" : "No")
            ),
            array(
                array("Priority", "static", "priority", $task->priority),
                array("Date Due", "static", "dt_due", formatDate($task->dt_due)),
            	array("Assigned To", "static", "assignee_id", $assigned)
                	
            ),
			array(
				array("Estimated hours", "static", "estimate_hours", $task->estimate_hours),
				array("Effort", "static", "effort", $task->effort),
			),
            array(
            	array("Description", "static", "description", $task->description)
            ),
        )
    );


    if (empty($p['id'])) {
    	History::add("New Task");
    } else {
    	History::add("Task: {$task->title}", null, $task);
    }
    
    //add task rate
    if (!empty($task->id) && $task->canISetRate()) {
        $form['Task Details'][3][] = ['rate', 'static', 'rate',$task->rate];
    }
    
    $w->ctx("task", $task);
    $w->ctx("form", Html::multiColTable($form));
    
    $createdDate = '';
    if (!empty($task->id)) {
        $creator = $task->_modifiable->getCreator();
        $createdDate =  formatDate($task->_modifiable->getCreatedDate()) . (!empty($creator) ? ' by <strong>' . @$creator->getFullName() . '</strong>' : '');
    }
    $w->ctx('createdDate', $createdDate);

    // Subscribers
    if (!empty($task->id)) {
        $task_subscribers = $task->getSubscribers();

        
        $w->ctx('subscribers', $task_subscribers);
    }

    ///////////////////
    // Notifications //
    ///////////////////
    
    $notify = null;
    // If I am assignee, creator or task group owner, I can get notifications for this task
    if (!empty($task->id) && $task->getCanINotify()) {

        // get User set notifications for this Task
        $notify = $w->Task->getTaskUserNotify($w->Auth->user()->id, $task->id);
        if (empty($notify)) {
            $logged_in_user_id = $w->Auth->user()->id;
            // Get my role in this task group
            $me = $w->Task->getMemberGroupById($task->task_group_id, $logged_in_user_id);
            
            $type = "";
            if ($task->assignee_id == $logged_in_user_id) {
                $type = "assignee";
            } else if ($task->getTaskCreatorId() == $logged_in_user_id) {
                $type = "creator";
            } else if ($w->Task->getIsOwner($task->task_group_id, $logged_in_user_id)) {
                $type = "other";
            }

            if (!empty($type) && !empty($me)) {
                $notify = $w->Task->getTaskGroupUserNotifyType($logged_in_user_id, $task->task_group_id, strtolower($me->role), $type);
            }
        }

        // create form. if still no 'notify' all boxes are unchecked
        $form = array(
            "Notification Events" => array(
                array(array("","hidden","task_creation", "0")),
                array(
                    array("Task Details Update","checkbox","task_details", !empty($notify->task_details) ? $notify->task_details : null),
                    array("Comments Added","checkbox","task_comments", !empty($notify->task_comments) ? $notify->task_comments : null)
                ),
                array(
                    array("Time Log Entry","checkbox","time_log", !empty($notify->time_log) ? $notify->time_log : null),
                    array("Task Data Updated","checkbox","task_data", !empty($notify->task_data) ? $notify->task_data : null)
                ),
                array(array("Documents Added","checkbox","task_documents", !empty($notify->task_documents) ? $notify->task_documents : null))
            )
        );

        $w->ctx("tasknotify", Html::multiColForm($form, $w->localUrl("/task/updateusertasknotify/".$task->id),"POST"));
    }
    
}