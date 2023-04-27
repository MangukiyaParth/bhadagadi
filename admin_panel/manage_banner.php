<?php
include 'header.php';
include 'sidebar.php';

$userObj = $gh->get_session("user");
$user_id = $userObj["id"];
$formname = "banner_form";

$include_javscript_at_bottom .= '<script src="' . $gh->auto_version('js/manage_datatable.js') . '"></script>';
$include_javscript_at_bottom .= '<script src="' . $gh->auto_version('js/manage_banner.js') . '"></script>';
$include_javscript_library_before_custom_script_at_bottom .= "<script>
	var ORIG_MODULE_NAME = 'Banner';
	var MODULE_KEY = 'banner';
	var FORM_NAME = '$formname';
</script>";

?>
<style>
    .descr p {
        text-align: justify;
        max-height: 105px;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 5;
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
                        <div class="page-title-right pr-5">
                            <button class="btn btn-info" id="addBtn" onclick="changeView('form', '<?php echo $formname ?>')"> Add </button>
                            <button class="btn btn-info" id="backBtn" onclick="changeView('details')" style="display: none;"> Back </button>
                        </div>
                        <h4 class="page-title">Banner</h4>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <div id="formDiv" style="display: none;">
                                <form id="<?php echo $formname ?>" class="needs-validation" method="POST" novalidate>
                                    <input type="hidden" id="id">
                                    <div class="mb-3 offset-sm-2 col-sm-8">
                                        <div class="row">
                                            <div class="col-sm-9">
                                                <label class="form-label" for="image">Image</label>
                                                <!-- Preview -->
                                                <input type="file" class="form-control" id="file" placeholder="file" style="width: 105px">
                                                <div class="invalid-feedback"> Please select Image. </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <img id="image_preview" src="#" alt="image Preview" style="height: 100px; border-radius: 10px; max-width: 100%" />
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary offset-sm-9" type="submit">Submit</button>
                                </form>
                            </div>
                            <div class="table-responsive mt-3" id="detailsDiv">
                                <table id="datatable" class="table table-striped dt-responsive nowrap table-striped w-100">
                                    <thead>
                                        <tr>
                                            <th width="60%">Image</th>
                                            <th width="20%" class="text-center">Active?</th>
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
<?php
include 'footer.php';
?>