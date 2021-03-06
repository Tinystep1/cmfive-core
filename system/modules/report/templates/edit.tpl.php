<div class="tabs">
    <div class="tab-head">
        <a href="#report"><?php echo !empty($report->id) ? "Edit" : "Create"; ?> Report</a>
        <?php if (!empty($report->id)) : ?>
            <a href="#code">SQL</a>
            <a href="#templates">Templates</a>
            <a href="#members">Members</a>
        <?php endif;?>
        <a href="#database">View Database</a>
    </div>
    <div class="tab-body">
        <div id="report" class="clearfix">
            <?php echo ($btnrun ?? "") . ($duplicate_button ?? "") . $report_form; ?>
        </div>
        <?php if (!empty($report->id)) : ?>
            <div id="code" class="clearfix">
                <?php echo $btnrun . $sql_form; ?>
            </div>
            <div id="templates">
                <p>You can add special templates to render the data. Create a <a href="/admin-templates">System Template in Admin</a> and set the
                module to <b>report</b>, it can then be selected here.</p>
                <p>The template processer uses the Twig language, you can find more information about this on
                the <a href="">Twig Website</a>.</p>
                <p>A good first step when creating a new template, is to look at the data. You can use the following
                twig statement in your template to do this:</p>
                <pre>{{dump(data)}}</pre>
                <p></p>
                <?php echo Html::box("/report-templates/edit/{$report->id}", "Add Template", true); ?>
                <?php echo !empty($templates_table) ? $templates_table : ""; ?>
            </div>
            <div id="members" style="display: none;" class="clearfix">
                <?php echo Html::box("/report/addmembers/" . $report->id, " Add New Members ", true) ?>
                <?php echo $viewmembers; ?>
            </div>
        <?php endif;?>
        <div id="database" style="display: none;" class="clearfix">
            <?php echo $dbform; ?>
        </div>
    </div>
</div>

<script language="javascript">

    var categories = <?php echo json_encode($category_config ?? []); ?>

    $('#module').change(function() {
        var option_string = '<option value="">-- Select --</option>';

        if (categories.hasOwnProperty($(this).val().toLowerCase())) {
            var _categories = categories[$(this).val().toLowerCase()];

            Object.keys(_categories).forEach(function(key) {
                option_string += '<option value="' + key + '">' + _categories[key] + '</option>';
            });

            $("#category").removeAttr('disabled');
        } else {
            $("#category").attr('disabled', 'disabled');
        }

        $("#category").html(option_string);
    });

    $.ajaxSetup({
        cache: false
    });

    $("#dbtables").change(function() {
        $.getJSON("/report/taskAjaxSelectbyTable?id=" + $("#dbtables option:selected").val(), function(result) {
            $('#dbfields').html(result);
        });
    });

</script>