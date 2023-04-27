<?php

    include('header.php');
    $include_javscript_at_bottom .= '<script src="'.$gh->auto_version('js/login.js').'"></script>';
    $include_javscript_library_before_custom_script_at_bottom .= '<script type="text/javascript">
    var MODULE_NAME = "login";
    </script>';
?>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header pt-2 pb-2 text-center bg-primary">
                            <a href="index.html">
                                <span><img src="assets/images/logo.png" alt="logo" height="100"></span>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">Sign In</h4>
                                <p class="text-muted mb-4">Enter your username and password to access admin panel.</p>
                            </div>

                            <form method="post">

                                <div class="mb-3">
                                    <label for="tb_username" class="form-label">Username </label>
                                    <input class="form-control" type="text" id="tb_username" required="" placeholder="Username">
                                </div>

                                <div class="mb-3">
                                    <label for="tb_password" class="form-label">Password</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="tb_password" class="form-control" required="" placeholder="Password">
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-primary" type="button" onclick="return login_user();"> Log In </button>
                                </div>
                            </form>
                        </div> 
                        <!-- end card-body -->
                    </div>
                    <!-- end card -->
                </div> 
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

<?php
    include('footer.php')
?>
