<div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="staticBackdropLabel">Select Contact to Merge</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="mergeForm" method="post">
            <div class="modal-body">
                <div class="tbl_select_contact">
                    <input type="hidden" name="secondary_id" id="secondary_id" value="<?= $id ?>">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <?php if (!empty($contacts)): ?>
                                <?php foreach ($contacts as $c): 
                                    $profileUrl  = base_url('uploads/profile/no_img.png');
                                    $profilePath = FCPATH . 'uploads/profile/' . $c->profile_image;
                                    if((!empty($c->profile_image) && file_exists($profilePath))):
                                        $profileUrl  = base_url('uploads/profile/' . $c->profile_image);
                                    endif;
                                    ?>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <div class="checkbox checkbox-success">
                                                <input type="checkbox" name="master_id" class="checkboxes check-child"
                                                    value="<?= $c->id ?>"
                                                    data-id="<?= $c->id ?>">
                                                <label for="<?= $c->id ?>" style="padding-left: 0;"></label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-flex">
                                        <div class="col-md-6 avatar-wrapper me-4">
                                            <img src="<?= $profileUrl ?>" alt="<?= $c->name ?>">
                                        </div>
                                        <div>
                                            <strong><?= ucwords($c->name) ?></strong><br>
                                            <strong>Email: </strong><?= $c->email ?><br>
                                            <strong>Phone: </strong><?= $c->phone ?></td>
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
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" id="submitMerge">Merge</button>
            </div>
        </form>
    </div>
</div>
