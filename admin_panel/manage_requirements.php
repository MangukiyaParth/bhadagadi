<?php
include 'header.php';
include 'sidebar.php';

$userObj = $gh->get_session("user");
$user_id = $userObj["id"];
$origin_module_name = "Requirements";

$include_javscript_at_bottom .= '<script src="' . $gh->auto_version('js/manage_datatable.js') . '"></script>';
$include_javscript_at_bottom .= '<script src="' . $gh->auto_version('js/manage_requirements.js') . '"></script>';
$include_javscript_library_before_custom_script_at_bottom .= "<script>
	var ORIG_MODULE_NAME = 'User';
	var MODULE_KEY = 'user';
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
                                            <th width="10%" class="text-start">Active Plan</th>
                                            <th width="10%" class="text-end">Action</th>
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

<div class="modal fade" id="requirementDetailModal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-full-width" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scrollableModalTitle">Requirement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body ps-5 pe-5 pt-4 pb-4">
                <div class="row">
                    <div class="col-sm-12 text-center" id="requirementData">

                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
include 'footer.php';
?>