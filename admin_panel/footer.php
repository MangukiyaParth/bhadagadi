<?php
	$user = $gh->get_session("user");
	$include_javscript_at_bottom = '<script src="' . $gh->auto_version('js/common.js') . '"></script>'.$include_javscript_at_bottom;
	$include_javscript_at_bottom = str_replace('<script ', '<script defer ', $include_javscript_at_bottom);
	include 'theme_settings.php';
?>
	<footer class="footer <?php if($current_page == 'login.php'){ echo 'footer-alt'; } ?>">
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12 <?php if($current_page != 'login.php'){ echo 'text-md-end'; } ?> footer-links d-none d-md-block">
					<script>document.write(new Date().getFullYear())</script> © Bhadagadi
				</div>
			</div>
		</div>
	</footer>

<div id="delete_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="ri-alert-line h1 text-warning"></i>
                    <h4 class="mt-2">Are you sure?</h4>
                    <p class="mt-3">you want to delete data?</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal" onclick="delete_current_record()">Continue</button>
                    <button type="button" class="btn btn-default my-2" data-bs-dismiss="modal" onclick="PRIMARY_ID = 0;">close</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
	
<!-- Vendor js -->
<script src="assets/js/vendor.min.js"></script>



<!-- Typehead -->
<script src="assets/js/handlebars.min.js"></script>
<script src="assets/js/typeahead.bundle.min.js"></script>

<!-- nprogress Js -->
<script defer src="assets/js/nprogress.js" ></script>
<script defer src="assets/js/jstz.min.js" ></script>
<script defer src="assets/js/moment.min.js" ></script>
<script defer src="assets/js/daterangepicker.js"></script>
<script defer src="assets/js/pnotify.custom.js"></script>
<script defer src="assets/js/jquery.dataTables.min.js"></script>
<script defer src="assets/js/dataTables.bootstrap5.min.js"></script>
<script defer src="assets/js/select2.min.js"></script>

<!-- App js -->
<script src="assets/js/app.min.js"></script>
<?php

$include_javscript_library_before_custom_script_at_bottom = str_replace('<script ', '<script defer ', $include_javscript_library_before_custom_script_at_bottom);


echo $include_javscript_library_before_custom_script_at_bottom;
echo $include_javscript_at_bottom;
?>
</div>
<script src="assets/js/highlight.pack.min.js"></script>
<script src="assets/js/hyper-syntax.js"></script>
<!-- fileupload js -->
<script src="assets/js/dropzone.min.js"></script>
<script src="assets/js/component.fileupload.js"></script>
</body>
</html>