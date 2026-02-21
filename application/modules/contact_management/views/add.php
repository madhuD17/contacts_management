<?php $this->load->view('layout/header'); ?>

<h4><?= isset($contact) ? 'Edit Contact' : 'Add Contact' ?></h4>

<form method="post" id="contactForm" enctype="multipart/form-data" class="card p-4 shadow-sm">
    <input type="hidden" name="action" value="<?= $action ?>">
    <input type="hidden" name="id" value="<?= isset($contact->id) ? $contact->id : '' ?>">
    <div class="row mb-3">
        <div class="col-md-6">
            <label>Name</label> <span class="error">*</span>
            <input type="text" name="name" class="form-control"
                   value="<?= isset($contact) ? $contact->name : '' ?>">
        </div>
        <div class="col-md-6">
            <label>Email</label> <span class="error">*</span>
            <input type="email" name="email" class="form-control"
                   value="<?= isset($contact) ? $contact->email : '' ?>">
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label>Phone</label> <span class="error">*</span>
            <input type="text" name="phone" class="form-control"
                   value="<?= isset($contact) ? $contact->phone : '' ?>">
        </div>
        <div class="col-md-6">
            <label>Gender</label> <span class="error">*</span><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" value="male"
                    <?= (isset($contact) && $contact->gender == 'male') ? 'checked' : '' ?>>
                <label class="form-check-label">Male</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" value="female"
                    <?= (isset($contact) && $contact->gender == 'female') ? 'checked' : '' ?>>
                <label class="form-check-label">Female</label>
            </div>
            <span class="invalid-radio"></span>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label>Profile Image</label>
            <input type="file" name="profile_image" class="form-control">
            <?php if (!empty($contact->profile_image)): ?>
            <p>
                <?= $contact->profile_image ?>:
                <a href="<?= base_url('uploads/profile/'.$contact->profile_image) ?>" target="_blank">
                    View
                </a>
            </p>
            <input type="hidden" name="old_file" value="<?= $contact->profile_image ?>">
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label>Document</label>
            <input type="file" name="document_file" class="form-control">
            <?php if (!empty($contact->document_file)): ?>
            <p>
                <?= $contact->document_file ?>:
                <a href="<?= base_url('uploads/document/'.$contact->document_file) ?>" target="_blank">
                    View
                </a>
            </p>
            <?php endif; ?>
        </div>
    </div>
    <?php if (!empty($custom_fields)): ?>
    <h4>Custom Fields</h4>
    <div class="row mb-3">
        <?php foreach ($custom_fields as $field): ?>
            <div class="col-md-6">
                <label><?= $field->field_label ?></label><?= ($field->is_required == 1) ? ' <span class="error">*</span>' : ''; ?>
                <?php if ($field->field_type == 'textarea'): ?>
                    <textarea name="custom_fields[<?= $field->id ?>]" class="form-control mb-2"><?= isset($field->field_value) ? $field->field_value : '' ?></textarea>
                <?php else : ?>
                    <input type="<?= $field->field_type ?>" class="form-control mb-2"
                           name="custom_fields[<?= $field->id ?>]" value="<?= isset($field->field_value) ? $field->field_value : '' ?>" >
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <button class="btn btn-primary me-md-2" type="submit">Save</button>
        <a href="<?= site_url('contact_management') ?>" class="btn btn-primary" type="button">Cancel</a>
    </div>
</form>
<?php $this->load->view('layout/footer'); ?>
<script>
    $(document).ready(function () {
        $.validator.addMethod("validImage", function(value, element) {
            if (element.files.length === 0) return false;
            var ext = element.files[0].name.split('.').pop().toLowerCase();
            return $.inArray(ext, ['jpg','jpeg','png']) !== -1;
        }, "Please upload a valid image (jpg, jpeg, png)");

        $.validator.addMethod("validDoc", function(value, element) {
            if (element.files.length === 0) return false;
            var ext = element.files[0].name.split('.').pop().toLowerCase();
            return $.inArray(ext, ['pdf','doc','docx']) !== -1;
        }, "Please upload a valid document (pdf, doc, docx)");

        $("#contactForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 30
                },

                email: {
                    required: true,
                    minlength: 5,
                    maxlength: 50,
                    email: true
                },

                phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                    digits: true
                },

                gender: {
                    required: true
                },

                profile_image: {
                    required: true,
                    validImage: true
                },

                document: {
                    required: true,
                    validDoc: true
                }
            },

            messages: {
                name: {
                    required: "Name is required"
                },
                email: {
                    required: "Email is required",
                    email: "Enter valid email"
                },
                phone: {
                    required: "Phone is required",
                    digits: "Only numbers allowed"
                },
                gender: {
                    required: "Please select gender"
                }
            },

            errorElement: "div",
            errorClass: "invalid-error",
            errorPlacement: function (error, element) {
                if (element.attr("type") === "radio") {
                    error.appendTo(".invalid-radio");
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                let formData = new FormData(form);
                $.ajax({
                    url: "<?= site_url('contact_management/save'); ?>",
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
                                window.location.href = "<?= base_url('contact_management') ?>";
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
