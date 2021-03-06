<?php

function process_GET(Web $w) {

	$w->setLayout(null);
	$p = $w->pathMatch("id");
	$id = $p["id"];

	if ($id) {
		$processors = $w->Channel->getProcessors($id);
		if (!empty($processors)) {
			foreach($processors as $processor) {
				$processor_class = $processor->retrieveProcessor();
				$processor_class->process($processor);
			}

			$w->Channel->markMessagesAsProcessed($id);
		}
	} else {
		$w->out("No channel found.");
	}
}