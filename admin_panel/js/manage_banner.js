//
// DataTables initialisation
//
var table;
jQuery(function () {
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#image_preview').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#file").change(function () {
        readURL(this);
    });
    get_data();
});

function get_data() {
    table = $('#datatable').DataTable({
        processing: true,
        serverSide: true,
        fixedHeader: true,
        autoWidth: false,
        pagingType: "full_numbers",
        responsive: true,
        language: { paginate: { previous: "<i class='mdi mdi-chevron-left'>", next: "<i class='mdi mdi-chevron-right'>" } },
        drawCallback: function () { $(".dataTables_paginate > .pagination").addClass("pagination-rounded") },
        ajax: $.fn.dataTable.pipeline({
            url: API_SERVICE_URL,
            pages: 1, // number of pages to cache
            operation: "get_banners",
            token: CURRENT_USER_TOKEN
        }),
        columns: [
            // { data: 'file', name: 'file', "width": "70%" },
        ],
        "columnDefs": [{
            "targets": 0,
            "className": "text-left",
            "data": "file",
            "render": function (data, type, row, meta) {
                return type === 'display' ? '<img src="' + WEB_API_FOLDER + data + '" height="100px" style="border-radius: 10px; max-width: 200px;">' : "";
            }
        },
        {
            "targets": 1,
            "className": "active",
            "data": "is_active",
            "render": function (data, type, row, meta) {
                var status = (data == 1) ? 'checked' : '';
                return type === 'display' ? '<div class="outerDivFull" >\
                                                <div class="switchToggle">\
                                                    <input class="active_status" '+ status + ' type="checkbox" onchange="updateBannerStatus(null, ' + row.id + ')" id="active_status' + row.id + '">\
                                                    <label for="active_status'+ row.id + '">Toggle</label>\
                                                </div>\
                                            </div>' : "";
            }
        },
        {
            "targets": 2,
            "className": "text-end",
            "data": "id",
            "render": function (data, type, row, meta) {
                return type === 'display' ?
                    '<button class="btn btn-primary rounded-pill tbl-btn edit-btn" onclick="edit_record(' + meta.row + ')"><i class="ri-pencil-fill"></i></button> <button class="btn btn-danger rounded-pill tbl-btn" onclick="delete_record(' + row.id + ')"><i class="uil-trash-alt"></i></button>' : "";
            }
        }]
    });
}

$("#" + FORM_NAME).on('submit', function (e) {
    e.preventDefault();
    add_record();
});

async function add_record(data) {
    if ($('#file').val() == "" && $('#id').val() == 0) {
        showError("Please select image");
        hideLoading();
        return false;
    }
    var formData = new FormData()
    formData.append('operation', 'add_banner')
    formData.append('token', CURRENT_USER_TOKEN)
    formData.append('file', $('#file')[0].files[0])
    formData.append('id', $('#id').val())
    showLoading();
    $.ajax({
        type: "POST",
        url: API_SERVICE_URL,
        data: formData,
        dataType: 'json',
        "crossDomain": true,
        "headers": {},
        processData: false,
        contentType: false,
        success: async function (data) {
            if (data.status) {
                changeView('details');
                showMessage(data.message);
                resetValidation(FORM_NAME);
                hideLoading();
                await table.clearPipeline().draw();
            }
            else {
                hideLoading();
                showError(data.message);
            }
            return false;
        },
        fail: function (err) {
            hideLoading();
            showError(data.message);
            return false;
        }
    });
}

function edit_record(index) {
    if (TBLDATA.length > 0) {
        var currData = TBLDATA[index];
        $('#id').val(currData.id);
        $('#image_preview').attr('src', WEB_API_FOLDER + currData.file);
        changeView('form');
    }
}

function updateBannerStatus(data, id) {
    if (data && data != null && data.status == true) {
        hideLoading();
        showMessage(data.message);
        return false;
    }
    else if (data && data != null && data.status == false) {
        hideLoading();
        $("#active_status" + id).prop("checked", !$("#active_status" + id).prop("checked"));
        showError(data.message);
        return false;
    }
    else if (!data) {
        showLoading();
        var data = {
            operation: "update_banner_status"
            , id: id
        };
        doAPICall(data, function (res) { updateBannerStatus(res, id) });
    }
    return false;
}

function delete_record(id) {
    PRIMARY_ID = id;
    $("#delete_modal").modal('show');
}

async function delete_current_record(data) {

    if (data && data != null && data.status == true) {
        hideLoading();
        PRIMARY_ID = 0;
        showMessage(data.message);
        await table.clearPipeline().draw();
        return false;
    }
    else if (data && data != null && data.status == false) {
        hideLoading();
        PRIMARY_ID = 0;
        showError(data.message);
        return false;
    }
    else if (!data) {
        showLoading();
        var data = {
            operation: "delete_banner"
            , id: PRIMARY_ID
        };
        doAPICall(data, delete_current_record);
    }
    return false;
}
