<?php $this->load->view('layout/header'); ?>

<div class="d-flex justify-content-between mb-3">
    <h4>Custom Field</h4>
    <a href="<?= site_url('custom_fields/create') ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i> Add Custom Field
    </a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Field Name</th>
            <th>Field Type</th>
            <th>Is Required</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($CustomFields)): ?>
            <?php foreach ($CustomFields as $c): ?>
            <tr>
                <td><?= $c->field_label ?></td>
                <td><?= ucfirst($c->field_type) ?></td>
                <td><?= ($c->is_required == 1) ? 'Yes': 'No' ?></td>
                <td>
                    <div class="dropdown action_menu">
                        <a class="p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu">
                            <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= site_url('custom_fields/edit/'.$c->id); ?>"><i class="fa fa-edit"></i> Edit</a></li>
                            <li><a class="dropdown-item delete-btn" href="javascript:void(0);" data-id="<?= $c->id; ?>"><i class="fa fa-trash"></i> Delete</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">No contacts found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php $this->load->view('layout/footer'); ?>
<script>
    $(document).on('click', '.delete-btn', function () {
        let id = $(this).data('id');
        bootbox.confirm({
            size: "small",
            title: '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Confirm',
            message: "Are you sure you want to delete?",
            callback: function(result)
            {
                if(result==true)
                {
                    $.ajax({
                        url: "<?= site_url('customfields/delete'); ?>",
                        type: "POST",
                        data: { id: id },
                        dataType: "json",
                        success: function (response) {
                            if (response.status == 'success') {
                                toastr.success("Custom field deleted successfully.");
                                location.reload();
                            } else {
                                toastr.error("Somethig went wrong.");
                            }
                        }
                    });
                }
            }
        });
    });
</script>
