<?php
include 'header.php';
include 'sidebar.php';

$userObj = $gh->get_session("user");
$user_id = $userObj["id"];
$formname = "user_form";
$origin_module_name = "User";

$include_javscript_at_bottom .= '<script src="' . $gh->auto_version('js/manage_datatable.js') . '"></script>';
$include_javscript_at_bottom .= '<script src="' . $gh->auto_version('js/manage_users.js') . '"></script>';
$include_javscript_library_before_custom_script_at_bottom .= "<script>
	var ORIG_MODULE_NAME = 'User';
	var MODULE_KEY = 'user';
	var FORM_NAME = '$formname';
</script>";

?>
<style>
    .descr {
        text-align: justify;
        max-height: 40px;
        overflow: hidden;
        font-size: 12px;
        color: #999;
        text-overflow: ellipsis;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .doc-title {
        font-size: 18px;
    }
    .img-Div {
        width: 100%;
        height: 250px;
        border-radius: 10px;
    }
    .img-Div:not(.sample-img) {
        box-shadow: 4px 4px 12px 2px #CDCDCD;
    }
    .img-Div:not(.sample-img):hover {
        box-shadow: 4px 4px 12px 2px #9A9A9A;
        border-radius: 10px;
    }
    .sample-img {
        background-image: url(assets/images/icon-photo.png);
        background-position: center;
        background-repeat: no-repeat;
        background-size: contain;
        border: 2px dashed #EEE;
        border-radius: 10px;
    }
    .img-Div img{
        width: 100%;
        height: 100%;
        border-radius: 10px;
        /* object-fit: contain; */
        cursor: pointer;
    }
    #documentPrevieModal img {
        height: calc(100vh - 200px);
        object-fit: fill;
    }
    span.user_status:before{
        display: inline-block;
        content: ' ';
        height: 12px;
        width: 12px;
        background: #EEE;
        margin-right: 10px;
        border-radius: 6px;
    }
    span.user_status.verified:before{
        background: #0acf97 !important;
    }
    span.user_status.pending:before{
        background: #ffc35a !important;
    }
    span.user_status.rejected:before{
        background: #fa5c7c !important;
    }
    span.user_status.suspended:before{
        background: #dc3545 !important;
    }
</style>
<div class="content-page">
    <div class="content">
        
        <!-- Start Content-->
        <div class="container-fluid">

            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title"><?php echo $origin_module_name; ?></h4>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive mt-3" id="detailsDiv">
                                <table id="datatable" class="table table-striped dt-responsive nowrap table-striped w-100">
                                    <thead>
                                        <tr>
                                            <th width="20%">Name</th>
                                            <th width="20%" class="text-start">Contact</th>
                                            <th width="20%" class="text-start">Business</th>
                                            <th width="20%" class="text-start">Location</th>
                                            <th width="20%" class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                </table>   
                            </div>

                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div>
            <!-- end row -->

        </div>
        <!-- container -->

    </div>
    <!-- content -->

</div>

<div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-full-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollableModalTitle">Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body ps-5 pe-5 pt-4 pb-5">
                <form>
                    <div class="mb-4 row">
                        <label for="colFormLabel" class="col-sm-1 col-form-label">Status</label>
                        <div class="col-sm-3">
                            <select class="form-select" id="user_status">
                                <option value="0">Not Verified</option>
                                <option value="2">Pending</option>
                                <option value="1">Verified</option>
                                <option value="3">Rejected</option>
                                <option value="4">Suspended</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button type="button" class="btn btn-primary" id="change_status">Apply</button>
                        </div>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="doc-title text-center">DL Front</div>
                        <div class="img-Div" id="dl_front"></div>
                    </div>
                    <div class="col-sm-3">
                        <div class="doc-title text-center">DL Back</div>
                        <div class="img-Div" id="dl_back"></div>
                    </div>
                    <div class="col-sm-3">
                        <div class="doc-title text-center">Adhar Front</div>
                        <div class="img-Div" id="adhar_front"></div>
                    </div>
                    <div class="col-sm-3">
                        <div class="doc-title text-center">Adhar Back</div>
                        <div class="img-Div" id="adhar_back"></div>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" id="documentPrevieModal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-full-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollableModalTitle">Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body ps-5 pe-5 pt-4 pb-4">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <img src="" />
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
include 'footer.php';
?>