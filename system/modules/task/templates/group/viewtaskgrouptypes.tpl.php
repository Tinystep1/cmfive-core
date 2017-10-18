<div class="tabs">
    <div class="tab-head">
        <a href="#dashboard">Task Groups</a>
        <a href="#create">New Task Group</a>
    </div>
    <div class="tab-body">
        <div id="dashboard">
            <?php echo Html::filter('Filter TaskGroups', $filter_data, "/task-group/viewtaskgrouptypes#dashboard", 'GET');
                echo Html::paginatedTable($table_header, $table_data, $page, $page_size, $total_results, "/task-group/viewtaskgrouptypes#dashboard", !empty($sort) ? $sort : null, !empty($sort_direction) ? $sort_direction : null, "viewtaskgrouptypes__page", "viewtaskgrouptypes__page-size", "total_results", "viewtaskgrouptypes__sort", "viewtaskgrouptypes__sort-direction"); ?>
        </div>
        <div id="create" class="clearfix">
            <?php echo $creategroup; ?>
        </div>
    </div>
</div>
<script language="javascript">
    // On change of task group type, get the task types and populate the select,
    // the do the same for priority
    $(document).ready(function() {
        $("#task_group_type").on("change", function(event) {
            var task_group_type = $("#task_group_type").val();
            $.getJSON("/task-group/ajaxSelectTaskGroupType/" + task_group_type,
                function (result) {
                    $("#default_task_type").html(result[0]);
                    $("#default_priority").html(result[1]);
                }
            );
       });        
    });            
</script>