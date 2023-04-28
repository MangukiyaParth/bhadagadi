//
// DataTables initialisation
//
var table;
jQuery(function () {
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
            operation: "get_users",
            token: CURRENT_USER_TOKEN
        }),
        columns: [
            { data: 'name', name: 'name', "width": "20%" },
        ],
        "columnDefs": [{
            "targets": 0,
            "className": "text-left",
            "render": function (data, type, row, meta) {
                var class_color = '';
                switch (row.account_status) {
                    case '1': //Verified
                        class_color = 'verified';
                        break;

                    case '2': //Pending
                        class_color = 'pending';
                        break;

                    case '3': //Rejected
                        class_color = 'rejected';
                        break;

                    case '4': //Suspended
                        class_color = 'suspended';
                        break;
                }
                return type === 'display' ? '<span class="user_status ' + class_color + '"></span><span>' + row.name + '</span>' : "";
            }
        },
        {
            "targets": 1,
            "className": "text-left",
            "render": function (data, type, row, meta) {
                return type === 'display' ? '<span><b>Phone: </b>' + row.phone + '</span><br/><span><b>Email: </b>' + row.email + '</span>' : "";
            }
        },
        {
            "targets": 2,
            "className": "text-left",
            "render": function (data, type, row, meta) {
                return type === 'display' ? '<span><b>' + row.business_name + '</b></span><br/><span class="descr">' + row.business_description + '</span>' : "";
            }
        },
        {
            "targets": 3,
            "className": "text-left",
            "render": function (data, type, row, meta) {
                return type === 'display' ? '<span>' + row.city + ', ' + row.state + '</span>' : "";
            }
        },
        {
            "targets": 4,
            "className": "text-end",
            "data": "id",
            "render": function (data, type, row, meta) {
                return type === 'display' ?
                    '<button class="btn btn-primary rounded-pill tbl-btn edit-btn" onclick="view_documents(' + meta.row + ')"><i class="uil-image"></i></button>' : "";
            }
        }]
    });
}

function view_documents(index) {
    if (TBLDATA.length > 0) {
        var currData = TBLDATA[index];
        PRIMARY_ID = currData.id;
        $("#dl_front").html("<img class='zoom-img' src='" + WEB_API_FOLDER + currData.dl_front + "' />").removeClass('sample-img');
        $("#dl_back").html("<img class='zoom-img' src='" + WEB_API_FOLDER + currData.dl_back + "' />").removeClass('sample-img');
        $("#adhar_front").html("<img class='zoom-img' src='" + WEB_API_FOLDER + currData.adhar_front + "' />").removeClass('sample-img');
        $("#adhar_back").html("<img class='zoom-img' src='" + WEB_API_FOLDER + currData.adhar_back + "' />").removeClass('sample-img');
        $("#documentModal").modal('show');

        $(".zoom-img").on('click', function () {
            $("#documentPrevieModal img").attr('src', $(this).attr('src'))
            $("#documentPrevieModal").modal('show');
        })
    }
}

function change_status(id) {
    PRIMARY_ID = id;
    $("#delete_modal").modal('show');
}

async function delete_current_record(data) {

    if (data && data != null && data.success == true) {
        hideLoading();
        PRIMARY_ID = 0;
        showMessage(data.message);
        await table.clearPipeline().draw();
        return false;
    }
    else if (data && data != null && data.success == false) {
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
