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
        $("#dl_front").html('').addClass('sample-img');
        $("#dl_back").html('').addClass('sample-img');
        $("#adhar_front").html('').addClass('sample-img');
        $("#adhar_back").html('').addClass('sample-img');
        if (currData.dl_front) {
            $("#dl_front").html("<img class='zoom-img' src='" + WEB_API_FOLDER + currData.dl_front.replace("/tmp/", "/tmp_thumb/") + "' />").removeClass('sample-img');
        }
        if (currData.dl_back) {
            $("#dl_back").html("<img class='zoom-img' src='" + WEB_API_FOLDER + currData.dl_back.replace("/tmp/", "/tmp_thumb/") + "' />").removeClass('sample-img');
        }
        if (currData.adhar_front) {
            $("#adhar_front").html("<img class='zoom-img' src='" + WEB_API_FOLDER + currData.adhar_front.replace("/tmp/", "/tmp_thumb/") + "' />").removeClass('sample-img');
        }
        if (currData.adhar_back) {
            $("#adhar_back").html("<img class='zoom-img' src='" + WEB_API_FOLDER + currData.adhar_back.replace("/tmp/", "/tmp_thumb/") + "' />").removeClass('sample-img');
        }
        $("#documentModal #user_status").val(currData.account_status);
        $("#documentModal").modal('show');

        $(".zoom-img").on('click', function () {
            $("#documentPrevieModal img").attr('src', $(this).attr('src').replace("/tmp_thumb/", "/tmp/"));
            $("#documentPrevieModal").modal('show');
        })
    }
}

$("#documentModal #change_status").on("click", function () {
    update_user_status();
});

async function update_user_status(data) {

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
            operation: "update_user_status"
            , id: PRIMARY_ID
            , user_status: $("#documentModal #user_status").val()
        };
        doAPICall(data, update_user_status);
    }
    return false;
}
