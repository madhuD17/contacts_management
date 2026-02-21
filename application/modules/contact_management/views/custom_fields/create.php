<?php $this->load->view('layout/header'); ?>
<h2><?= isset($field) ? 'Edit' : 'Add' ?> Custom Field</h2>
<div id="responseMsg"></div>
<form method="post" id="customFieldForm" class="card p-4 shadow-sm">
    <input type="hidden" name="c_id" value="<?= isset($field->id) ? $field->id : '' ?>" >
    <input type="hidden" name="action" value="<?= $action ?>" >
    <div class="row mb-3">
        <div class="col-md-6">
            <label>Field Label</label> <span class="error">*</span>
            <input type="text" name="field_label" class="form-control"
                   value="<?= isset($field) ? $field->field_label : '' ?>" >
        </div>
        <div class="col-md-6">
            <label>Field Type</label> <span class="error">*</span>
            <select name="field_type" class="form-control" >
                <?php
                $types = get_input_types();
                foreach ($types as $type):
                ?>
                    <option value="<?= $type ?>"
                        <?= (isset($field) && $field->field_type == $type) ? 'selected' : '' ?>>
                        <?= ucfirst($type) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_required" value="" id="checkChecked" <?= (isset($field) && $field->is_required) ? 'checked' : '' ?>>
                <label class="form-check-label" for="checkChecked">
                    Required
                </label>
            </div>
        </div>
    </div>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button class="btn btn-primary me-md-2" type="submit">Save</button>
        <a href="<?= site_url('custom_fields') ?>" class="btn btn-primary" type="button">Cancel</a>
    </div>
</form>
<?php $this->load->view('layout/footer'); ?>
<script>
$(document).ready(function () {
        $("#customFieldForm").validate({
            rules: {
                field_label: {
                    required: true,
                    minlength: 3,
                    maxlength: 30
                },
                field_type: {
                    required: true,
                },
            },
            messages: {
                field_label: {
                    required: "Field Label is required"
                },
                field_type: {
                    required: "Field Type is required",
                },
            },
            errorElement: "div",
            errorClass: "invalid-error",
            submitHandler: function(form) {
                let formData = new FormData(form);
                $.ajax({
                    url: "<?= site_url('custom_fields/save'); ?>",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(response) {
                        if (response.status === 'success') {
                            toastr.success(response.message);
                            form.reset();
                            setTimeout(function () {
                                window.location.href = "<?= base_url('custom_fields') ?>";
                            }, 3000);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error("Something went wrong.");
                    }
                });
                return false;
            }
        });
    });
</script>
