
<div style="width:400px">
	<?php echo (!empty($rep)) ? $rep->description : "No Rep for user"; ?>
    <?php echo (!empty($report)) ? $report : "" ?>
</div>		


<script language="javascript">
	$(document).ready(function() {
		$("#format").val("html");
	});
</script>