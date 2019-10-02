<?php

function show_form(\Web $w, $params)
{
    $form = $params["form"];
    if (empty($form)) {
        return;
    }

    $form_fields = $form->getFields();
    if (empty($form_fields)) {
        return;
    }

    $form_instance = null;
    $form_instances = $w->Form->getFormInstancesForFormAndObject($form, $params['object']);

    if (!empty($form_instances) && count($form_instances) > 0) {
        $form_instance = $form_instances[0];
    }

    $table_rows = [];
    foreach ($form_fields as $form_field) {
        $form_instance_field_value = null;

        if (!empty($form_instance)) {
            $form_instance_field_value = $w->Form->getFormValueForInstanceAndField($form_instance->id, $form_field->id);
        }

        $table_rows[] = [$form_field->name, "static", $form_field->technical_name, empty($form_instance_field_value) ? null : $form_instance_field_value->getMaskedValue()];
    }

    $table_data[$form->title] = [
        $table_rows,
    ];

    $form_instance_id = empty($form_instance) ? "" : $form_instance->id;
    $redirect_url = $params["redirect_url"] ?? "";
    $object = $params["object"] ?? "";
    $object_class = "";

    if (!empty($object)) {
        $object_class = get_class($object);
    }

    $edit_button = Html::box("/form-instance/edit/{$form_instance_id}?form_id={$form->id}&redirect_url={$redirect_url}&object_class={$object_class}&object_id={$object->id}", "Edit", true);

    $w->ctx("form", $form);
    $w->ctx("edit_button", $edit_button);
    $w->ctx("table", Html::multiColTable($table_data));
}