<?php
include 'header.php';
include 'sidebar.php';

$userObj = $gh->get_session("user");
$user_id = $userObj["id"];
$include_javscript_library_before_custom_script_at_bottom .= "<script>
	var ORIG_MODULE_NAME = 'Dashboard';
	var MODULE_KEY = 'index';
</script>";

?>
<div class="content-page">
    <div class="content">
        
        <!-- Start Content-->
        <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-xl-12 col-lg-12">
                    <div class="card">
                        <div class="card-header justify-content-between align-items-center">
                            <h4 class="header-title p-3 text-center">Welcom to Bhadagadi</h4>
                        </div>
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