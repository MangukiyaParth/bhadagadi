<?php
include 'header.php';
include 'sidebar.php';

$userObj = $gh->get_session("user");
$user_id = $userObj["id"];

$include_javscript_at_bottom .= '<script src="' . $gh->auto_version('js/manage_index.js') . '"></script>';
$include_javscript_library_before_custom_script_at_bottom .= "<script>
	var ORIG_MODULE_NAME = 'Dashboard';
	var MODULE_KEY = 'index';
</script>";

?>
<style>
    .card {
        border-radius: 10px;
    }

    .chart-reload:before {
        font-size: 20px !important;
    }
</style>
<div class="content-page">
    <div class="content">

        <!-- Start Content-->
        <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-xl-3 col-lg-4">
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <div class="income_cnt_loader"><?php include 'loader.php'; ?></div>
                            <i class='mdi mdi-currency-rupee float-end'></i>
                            <h6 class="text-uppercase mt-0">Today's Income</h6>
                            <h2 class="mt-2">₹ <span id="today_earning">0</span></h2>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <div class="income_cnt_loader"><?php include 'loader.php'; ?></div>
                            <i class='mdi mdi-currency-rupee float-end'></i>
                            <h6 class="text-uppercase mt-0">This week's Income</h6>
                            <h2 class="mt-2">₹ <span id="week_earning">0</span></h2>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <div class="income_cnt_loader"><?php include 'loader.php'; ?></div>
                            <i class='mdi mdi-currency-rupee float-end'></i>
                            <h6 class="text-uppercase mt-0">This Month's Income</h6>
                            <h2 class="mt-2">₹ <span id="month_earning">0</span></h2>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                </div> <!-- end col -->

                <div class="col-xl-9 col-lg-8">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Plan Income</h4>
                            <div dir="ltr">
                                <div class="income_cnt_loader"><?php include 'loader.php'; ?></div>
                                <div id="income_chart" class="apex-charts mt-3" data-colors="#0acf97"></div>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-xl-3 col-lg-4">
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <div class="income_cnt_loader"><?php include 'loader.php'; ?></div>
                            <i class='uil uil-users-alt float-end'></i>
                            <h6 class="text-uppercase mt-0">Today's Users</h6>
                            <h2 class="mt-2"><span id="today_user_cnt">0</span></h2>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <div class="income_cnt_loader"><?php include 'loader.php'; ?></div>
                            <i class='uil uil-users-alt float-end'></i>
                            <h6 class="text-uppercase mt-0">This week's Users</h6>
                            <h2 class="mt-2"><span id="week_user_cnt">0</span></h2>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                    <div class="card tilebox-one">
                        <div class="card-body">
                            <div class="income_cnt_loader"><?php include 'loader.php'; ?></div>
                            <i class='uil uil-users-alt float-end'></i>
                            <h6 class="text-uppercase mt-0">This Month's Users</h6>
                            <h2 class="mt-2"><span id="month_user_cnt">0</span></h2>
                        </div> <!-- end card-body-->
                    </div>
                    <!--end card-->
                </div> <!-- end col -->

                <div class="col-xl-9 col-lg-8">
                    <div class="card card-h-100">
                        <div class="card-body">
                            <h4 class="header-title mb-3">Users</h4>
                            <div dir="ltr">
                                <div class="income_cnt_loader"><?php include 'loader.php'; ?></div>
                                <div id="user_chart" class="apex-charts mt-3" data-colors="#0acf97"></div>
                            </div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div>
            </div>

        </div>
        <!-- container -->

    </div>
    <!-- content -->

</div>
<?php
include 'footer.php';
?>