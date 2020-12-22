<div class="image-container attachment text-center">
    <div class="image-container-overlay">
        <div class="row-fluid">
            <button href="#" data-reveal-id="attachment_modal_<?php echo $attachment->id; ?>" class="button expand" onclick="$('#img_<?php echo $attachment->id; ?>').attr('src', $('#img_<?php echo $attachment->id; ?>').attr('src-async'));">
                View
            </button>
        </div>
        <div class="row-fluid">
            <?php echo Html::box("/file/edit/" . $attachment->id . "?redirect_url=" . urlencode($redirect), "Edit", true, null, null, null, null, null, "button expand secondary"); ?>
        </div>
        <div class="row-fluid">
            <?php echo Html::b("/file/delete/" . $attachment->id . "?redirect_url=" . urlencode($redirect), "Delete", "Are you sure you want to delete this attachment?", null, false, "expand alert "); ?>
        </div>
    </div>
    <?php if ($attachment->isImage()) { ?>
        <img class="image-cropped" data-caption="<?php echo $attachment->title; ?>" src="<?php echo $attachment->getThumbnailUrl(); ?>">
    <?php } elseif ($attachment->isDocument()) { ?>
        <i class="fi-page-<?php echo $attachment->mimetype === "application/pdf" ? "pdf" : "doc"; ?>" style="font-size: 90pt;"></i>
    <?php } else { ?>
        <i class="fi-page" style="font-size: 90pt;"></i>
    <?php } ?>
</div>
<a href="#" data-reveal-id="attachment_modal_<?php echo $attachment->id; ?>">
    <div class="row-fluid clearfix text-center">
        <b>Title: </b><?php echo $attachment->title; ?>
    </div>
    <div class="row-fluid clearfix text-center">
        <b>Description: </b><?php echo strip_tags($attachment->description); ?>
    </div>
    <div class="row-fluid clearfix text-center">
        <?php if (!empty($owner)) {
            $contact = $owner->getContact();
            $buffer .= empty($contact) ? "" : "<b>Owner: </b> " . $contact->getFullname();
        } ?>
    </div>
</a>
<div id="attachment_modal_<?php echo $attachment->id; ?>" class="reveal-modal file__pdf-modal" data-reveal role="dialog">
    <div class="row-fluid panel" style="text-align: center;">
        <?php if ($attachment->isImage()) { ?>
            <img id="img_<?php echo $attachment->id; ?>" src-async="/file/atfile/<?php echo $attachment->id; ?>" alt="<?php echo $attachment->title; ?>" />
        <?php } elseif ($attachment->isDocument()) { ?>
            <?php echo $attachment->getDocumentEmbedHtml(); ?>
        <?php } else { ?>
            <i class="fi-page" style="font-size: 90pt;"></i>
        <?php } ?>
    </div>
    <h2 id="firstModalTitle" style="font-weight: lighter; text-align: center; border-bottom: 1px solid #777;"><?php echo $attachment->title; ?></h2>
    <p style="text-align: center;"><?php echo $attachment->description; ?></p>
    <div class="row-fluid">
        <div class="small-4 columns">
            <a href="/file/atfile/<?php echo $attachment->id; ?>" target="_blank" class="button expand" onclick="$('#attachment_modal_<?php echo $attachment->id; ?>').foundation('reveal', 'close');">Open in new tab</a>
        </div>
        <div class="small-4 columns">
            <a href="/file-image/metadata/<?php echo $attachment->id; ?>" target="_blank" class="button expand" onclick="$('#attachment_modal_<?php echo $attachment->id; ?>').foundation('reveal', 'close');">View metadata</a>
        </div>
        <div class='small-4 columns'>
            <a class="button expand secondary" onclick="$('#attachment_modal_<?php echo $attachment->id; ?>').foundation('reveal', 'close');" aria-label="Close">Close</a>
        </div>
    </div>
    <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>