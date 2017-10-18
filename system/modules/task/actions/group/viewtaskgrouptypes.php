<?php
function viewtaskgrouptypes_ALL(Web $w) {
	$w->Task->navigation($w, "Manage Task Groups");
	
	History::add("Manage Task Groups");

	// Get filter data
	$type = $w->sessionOrRequest("viewtaskgrouptypes__filter-type", null);
	if ($type == "-- Select --") {
		$type = null;
	}
    $show_inactive = $w->sessionOrRequest("viewtaskgrouptypes__filter-inactive", "0");
    $page = $w->sessionOrRequest("viewtaskgrouptypes__page", 1);
	$page_size = $w->sessionOrRequest("viewtaskgrouptypes__page-size", 20);
	$sort = $w->sessionOrRequest("viewtaskgrouptypes__sort", 'title');
	$sort_direction = $w->sessionOrRequest("viewtaskgrouptypes__sort-direction", 'asc');
	$w->ctx("page", $page);
	$w->ctx("page_size", $page_size);
	$w->ctx("sort", $sort);
	$w->ctx("sort_direction", $sort_direction);

    $reset = $w->request("reset");
    if (!empty($reset)) {
    	$type = null;
    	$w->sessionUnset("viewtaskgrouptypes__filter-type");
        $show_inactive = "0";
        $w->sessionUnset("viewtaskgrouptypes__filter-inactive");
    }
    
    $filter_data = [];
    $group_type_options = [["-- Select --", false]];
    $group_type_options = array_merge($group_type_options, $w->Task->getAllTaskGroupTypes());
    $filter_data[] = (new \Html\Form\Select([
            'id|name'   => "viewtaskgrouptypes__filter-type",
            'label'     => 'Type',
            'options'   => $group_type_options
    ]))->setSelectedOption($type);
    $filter_data[] = (new \Html\Form\Select([
            'id|name'   => "viewtaskgrouptypes__filter-inactive",
            'label'     => 'Include Inactive',
            'options'   => [['value' => '0', 'label' => 'No'], ['value' => '1', 'label' => 'Yes']]
    ]))->setSelectedOption($show_inactive);

    $w->ctx('filter_data', $filter_data);

    //count total taskgroups
    $total_taskgroups = $w->Task->countTaskGroups($show_inactive, $type);
	$w->ctx("total_results", $total_taskgroups);

    // Set up table header 
    $table_header = array(['title', "Title"], ["task_group_type", "Type"], ["description", "Description"], ["default_assignee", "Default Assignee"]);
    if ($show_inactive) {
    	$table_header[] = ["is_active", "Active"];
    }

    //get taskgroups for filter
    $task_groups = $w->Task->getTaskGroups($show_inactive, $type, $page, $page_size, $sort, $sort_direction);
    $table_data = [];
    if (!empty($task_groups)) {
    	foreach ($task_groups as $group) {
    		$row = [];
    		$row[] = Html::a(WEBROOT."/task-group/viewmembergroup/".$group->id,$group->title);
    		$row[] = $group->getTypeTitle();
    		$row[] = $group->description;
    		$row[] = $group->getDefaultAssigneeName();
    		if ($show_inactive) {
    			$row[] = $group->is_active ? "Yes" : "No" ;
    		}
    		$table_data[] = $row;
    	}
    }

    $w->ctx('table_header', $table_header);
    $w->ctx('table_data', $table_data);

	// // prepare column headings for display
	// $line = array(array("Title","Type", "Description", "Default Assignee"));

	// // if task group exists, display title, group type, description, default assignee and button for specific task group info
	// if ($task_groups) {
	// 	foreach ($task_groups as $group) {
	// 		$line[] = array(
	// 				Html::a(WEBROOT."/task-group/viewmembergroup/".$group->id,$group->title),
	// 				$group->getTypeTitle(),
	// 				$group->description,
	// 				$group->getDefaultAssigneeName(),
	// 		);
	// 	}
	// }
	// else {
	// 	// if no groups for this group type, say as much
	// 	$line[] = array("There are no Task Groups Configured. Please create a New Task Group.","","","","");
	// }

	// // display list of task groups in the target task group type
	// $w->ctx("dashboard",Html::table($line,null,"tablesorter",true));

	// // tab: new task group
	// // get generic task group permissions
	// $arrassign = $w->Task->getTaskGroupPermissions();
	// // unset 'ALL' given all can never assign a task
	// unset($arrassign[0]);

	// // set Is Task Active dropdown
	// $is_active = array(array("Yes","1"), array("No","0"));

	// $grouptypes = $w->Task->getAllTaskGroupTypes();
 //        $assignees = $w->Auth->getUsers();
 //        array_unshift($assignees,array("Unassigned","unassigned"));        

	// // build form to create a new task group within the target group type
	// $f = Html::form(array(
	// 		array("Task Group Attributes","section"),
	// 		array("Task Group Type","select","task_group_type",null,$grouptypes),
	// 		array("Title","text","title"),
	// 		array("Who Can Assign","select","can_assign",null,$arrassign),
	// 		array("Who Can View","select","can_view",null,$w->Task->getTaskGroupPermissions()),
	// 		array("Who Can Create","select","can_create",null,$w->Task->getTaskGroupPermissions()),
	// 		array("Active","select","is_active",null,$is_active),
	// 		array("","hidden","is_deleted","0"),
	// 		array("Description","textarea","description",null,"26","6"),
	// 		array("Default Task Type","select","default_task_type",null,null),
	// 		array("Default Priority","select","default_priority",null,null),
	// 		array('Automatic Subscription', 'checkbox', 'is_automatic_subscription', TaskGroup::$_DEFAULT_AUTOMATIC_SUBSCRIPTION)
	// 		//array("Default Assignee","select","default_assignee_id",null,$assignees),
	// ),$w->localUrl("/task-group/createtaskgroup"),"POST","Save");

	// // display form
	// $w->ctx("creategroup",$f);
}
