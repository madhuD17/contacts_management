<?php $this->load->view('layout/header'); ?>
<style>
    .remove-outline {
        caret-color: transparent !important;
        /* user-select: none !important; */
    }
</style>
<div class="d-flex justify-content-between mb-3">
    <h4>Contacts</h4>
    <div class="input-group search-box">
        <input
            type="text"
            id="search"
            class="form-control"
            placeholder="Search..."
        >
        <span class="input-group-text bg-white" id="searchBtn" style="cursor: pointer;">
            <i class="bi bi-search"></i>
        </span>
    </div>
    <a href="<?= site_url('contacts/create') ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i> Add Contact
    </a>
</div>
<div id="contact-table" class="remove-outline" style="max-height: calc(100vh - 200px); overflow-y: auto;"></div>

    <!-- Modal -->
<div class="modal fade" id="mergeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true"></div>
<?php $this->load->view('layout/footer'); ?>
<script>
    var table = new Tabulator("#contact-table", {
        layout:"fitColumns",
        columnDefaults: {
            hozAlign: "center",
            headerHozAlign: "center",
            vertAlign: "middle"
        },
        ajaxURL:"<?= base_url('contacts/index_data') ?>",
        ajaxConfig:"POST",
        ajaxParams:function()
        {
            return {
                search: document.getElementById("search").value
            };
        },
        // pagination:"remote",
        pagination: true,
        paginationMode: "remote",
        paginationSize:10,
        sortMode:"remote",
        ajaxResponse:function(url, params, response)
        {
            return response.data;
        },
        paginationDataReceived:
        {
            data:"data",
            last_page:"last_page"
        },
        initialSort:[
            {column:"id", dir:"desc"}
        ],
        columns:[
            {title:"Profile Image", field:"profile_image", width:100, hozAlign:"center", headerSort:false, formatter:function(cell){
                    let img = cell.getValue();
                    if(!img) {
                        string = "<?= base_url('uploads/profile/no_img.png') ?>";
                    } else {
                        string = "<?= base_url('uploads/profile/') ?>" + img;
                    }
                    return '<div class="profile-img-wrapper"><img src="' + string + '" class="rounded-circle" alt="Profile Image"></div>';
                }
            },
            {title:"Name", field:"name", width:130, formatter:function(cell){
                return '<div class="text_wrap">'+cell.getValue()+'</div>';
            }},
            {title:"Phone", field:"phone", width:130, formatter:function(cell){
                return '<div class="text_wrap">'+cell.getValue()+'</div>';
            }},
            {title:"Email", field:"email", width:200, formatter:function(cell){
                return '<div class="text_wrap">'+cell.getValue()+'</div>';
            }},
            {title:"Gender", field:"gender", formatter:function(cell){
                let value = cell.getValue();
                gender = (value == 'male') ? 'Male' : 'Female';
                return '<span>'+gender+'</span>';
            }},
            {title:"Status", field:"status", formatter:function(cell){
                let status = cell.getValue();
                if(status == 'merged'){
                    return `<span class="badge badge-merged">Merged</span>`;
                } else {
                    return `<span class="badge badge-success">Active</span>`;
                }
            }},
            {title:"Action", field:"id", hozAlign: "center", formatter:function(cell){
                let id = cell.getValue();
                let rowData = cell.getRow().getData();
                let status = rowData.status;
                url = "<?= site_url('contacts/edit/') ?>"+ id;
                string = '<div class="dropdown action_menu">';
                string += '<a class="p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Menu">';
                string += '<i class="bi bi-three-dots-vertical"></i></a>';
                string += '<ul class="dropdown-menu">';
                if(status == 'active') {
                    string += '<li><a class="dropdown-item" href="'+url+'"><i class="fa fa-edit"></i> Edit</a></li>';
                    string += '<li><a class="dropdown-item" href="javascript:void(0);" data-id="'+id+'" onclick="openMergeModal('+id+')"><i class="bi bi-sign-merge-right-fill"></i> Merge</a></li>';
                }
                string += '<li><a class="dropdown-item delete-btn" href="javascript:void(0);" data-id="'+id+'"><i class="fa fa-trash"></i> Delete</a></li>';
                string += '</ul></div>';
                return string;
            },
            headerSort:false},
        ],
    });

    // trigger search
    // document.getElementById("search").addEventListener("keyup", function(){
    //     table.setData();
    // });

    // trigger search on click
    document.getElementById("searchBtn").addEventListener("click", function () {
        let keyword = document.getElementById("search").value;

        if (keyword.trim() === "") {
            table.clearFilter();
        }
        table.setData();
    });

    document.getElementById("search").addEventListener("keyup", function (e) {
        if (e.key === "Enter") {
            document.getElementById("searchBtn").click();
        }
    });

    document.getElementById("search").addEventListener("input", function () {
        if (this.value === "") {
            document.getElementById("searchBtn").click();
        }
    });
    /* Open modal to select contact to merge */
    function openMergeModal(id)
    {
        $.ajax({
            url: '<?= site_url('mergeContacts/get_master_contacts') ?>',
            type: 'POST',
            dataType: 'json',
            data: {id: id},
            success: function(response)
            { 
                if(response.status == 'success')
                {
                    $('#mergeModal').html(response.html);
                    $('#mergeModal').modal('show');
                }
                else
                {
                    toastr.error("Something went wrong.");
                }
            }
        });
    }

    $('body').on('click', '#submitMerge', function (e) {
        e.preventDefault();
        bootbox.confirm({
            size: "small",
            title: '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Confirm',
            message: "Are you sure you want to Merge Contact?",
            callback: function(result)
            {
                if(result==true)
                {
                    $.ajax({
                        url: "<?= site_url('mergeContacts/merge_contact') ?>",
                        type: "POST",
                        data: $('#mergeForm').serialize(),
                        success: function (response) {
                            toastr.success("Contact merged successfully.");
                            $('#mergeModal').modal('hide');
                            table.setData();
                        },
                        error: function () {
                            toastr.error("Something went wrong.");
                        }
                    });
                }
            }
        });
    });

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
                        url: "<?= site_url('contacts/delete'); ?>",
                        type: "POST",
                        data: { id: id },
                        dataType: "json",
                        success: function (response) {
                            if (response.status == 'success') {
                                toastr.success("Contact deleted successfully.");
                                table.setData();
                            } else {
                                toastr.error("Something went wrong.");
                            }
                        }
                    });
                }
            }
        });
    });
</script>
