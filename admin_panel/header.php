<?php
require_once('ajax.php');
include_once("projectConst.php");

$locale = "en";
$userObj = (array) $gh->get_session("user");
if (isset($userObj) && is_array($userObj) && isset($userObj["language"])) {
	$locale = $userObj["language"];
}
global $translate;
$const = new ProjectConst();

$primary_id = (int)$gh->read("id");
$userObj = $gh->get_session("user");
$logo = "images/logo" . (IS_PRODUCTION ? "" : (IS_SANDBOX ? "_sandbox" : "_beta")) . ".png";
if (!empty($header_logo)) {
	$logo = $header_logo;
}

$current_file_name = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
$current_page = basename($_SERVER['PHP_SELF']);
$types = [];
$favicon_path = 'assets/images/favicon.ico';
$favicon_type = 'image/x-icon';
$logged_in_username = $gh->get_current_user("name");
$logged_in_email = $gh->get_current_user("email");
if ($current_module_name == "") {
	if ($current_file_name == "index.php") {
		$current_module_name = "Dashboard";
		$html_title = "Taxi Group: " . $current_module_name . ": " . str_replace("\'", "'", $logged_in_username) . " [" . $logged_in_email . "]";
	} elseif ($current_file_name == "login.php") {
		$html_title = "Taxi Group";
	} else {
		$html_title = "Taxi Group: " . str_replace("\'", "'", $logged_in_username) . " [" . $logged_in_email . "]";
	}
} else {
	if ($current_file_name == "index.php") {
		$current_module_name = "Dashboard";
	}
	$html_title = "Taxi Group: " . $current_module_name . ": " . str_replace("\'", "'", $logged_in_username) . " [" . $logged_in_email . "]";
}

$include_javscript_library_before_custom_script_at_bottom .= '<script type="text/javascript">
var PRIMARY_ID = ' . (empty($primary_id) ? 0 : $primary_id) . ';
var CURRENT_PAGE = "' . $current_page . '";
var LOGGED_IN_ROLE_ID = "' . ($userObj['role_id'] ?? 0) . '";
var IS_PRODUCTION="' . IS_PRODUCTION . '";
var IS_BETA="' . IS_BETA . '";
var LOCALE = "' . $locale . '";
var TBLDATA = [];
</script>';
?>
<!doctype html>
<html class="fixed sidebar-left-collapsed">

<head>

	<!-- Basic -->
	<meta charset="UTF-8">


	<title><?= $html_title ?></title>
	<meta name="keywords" content="Taxi Group" />
	<meta name="description" content="Taxi Group, User Panel">
	<meta name="google" content="notranslate">
	<link rel="icon" href="<?= $favicon_path; ?>" type="<?= $favicon_type; ?>" />
	<link rel="apple-touch-icon" sizes="57x57" href="<?= $favicon_path; ?>">
	<link rel="apple-touch-icon" sizes="60x60" href="<?= $favicon_path; ?>">
	<link rel="apple-touch-icon" sizes="72x72" href="<?= $favicon_path; ?>">
	<link rel="apple-touch-icon" sizes="76x76" href="<?= $favicon_path; ?>">
	<link rel="apple-touch-icon" sizes="114x114" href="<?= $favicon_path; ?>">
	<link rel="apple-touch-icon" sizes="120x120" href="<?= $favicon_path; ?>">
	<link rel="apple-touch-icon" sizes="144x144" href="<?= $favicon_path; ?>">
	<link rel="apple-touch-icon" sizes="152x152" href="<?= $favicon_path; ?>">
	<link rel="apple-touch-icon" sizes="180x180" href="<?= $favicon_path; ?>">
	<link rel="icon" type="<?= $favicon_type; ?>" sizes="192x192" href="<?= $favicon_path; ?>">
	<link rel="icon" type="<?= $favicon_type; ?>" sizes="32x32" href="<?= $favicon_path; ?>">
	<link rel="icon" type="<?= $favicon_type; ?>" sizes="96x96" href="<?= $favicon_path; ?>">
	<link rel="icon" type="<?= $favicon_type; ?>" sizes="16x16" href="<?= $favicon_path; ?>">

	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="<?= $favicon_path; ?>">
	<meta name="theme-color" content="#ffffff">

	<!-- Mobile Metas -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

	<link rel="shortcut icon" href="<?php echo ADMIN_PANEL_URL; ?>assets/images/favicon.ico">

	<link rel="stylesheet" href="<?php echo ADMIN_PANEL_URL; ?>assets/css/custom.css">
	<!-- Plugin css -->
	<link rel="stylesheet" href="<?php echo ADMIN_PANEL_URL; ?>assets/css/daterangepicker.css">
	<link rel="stylesheet" href="<?php echo ADMIN_PANEL_URL; ?>assets/css/pnotify.custom.css" />
	<link rel="stylesheet" href="<?php echo ADMIN_PANEL_URL; ?>assets/css/dataTables.bootstrap5.min.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo ADMIN_PANEL_URL; ?>assets/css/select2.min.css" type="text/css" />

	<link href="<?php echo ADMIN_PANEL_URL; ?>assets/css/app-saas.min.css" rel="stylesheet" type="text/css" id="app-style" />
	<link href="<?php echo ADMIN_PANEL_URL; ?>assets/css/icons.min.css" rel="stylesheet" type="text/css" />



	<!-- Web Fonts  -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

	<!-- Vendor CSS -->
	<?php echo $include_stylesheet_in_header; ?>

	<!-- Theme Config Js -->
	<script src="assets/js/hyper-config.js"></script>
	<script>
		var clevertap = null;
		var mixpanel = null;
		// Domain
		const domain = '<?= ADMIN_PANEL_URL ?>manage_projects.php';
		const domain_global = '<?= ADMIN_PANEL_URL ?>index.php';

		// MySQL API
		const notify_panel_url = '<?= ADMIN_PANEL_URL ?>';
	</script>
</head>

<body class="<?php if ($current_page == 'index') {
					echo 'authentication-bg';
				} ?>">
	<div class="wrapper">
		<?php if ($gh->get_current_user() && !in_array($current_page, $login_not_needed_pages) && !in_array($current_page, $header_not_needed_pages)) { ?>

			<!-- ========== Topbar Start ========== -->
			<div class="navbar-custom topnav-navbar">
				<div class="container-fluid detached-nav">

					<!-- Topbar Logo -->
					<div class="logo-topbar">
						<!-- Logo light -->
						<a href="index.html" class="logo-light">
							<span class="logo-lg">
								<img src="<?php echo ADMIN_PANEL_URL; ?>assets/images/logo.png" alt="logo" height="22">
							</span>
							<span class="logo-sm">
								<img src="<?php echo ADMIN_PANEL_URL; ?>assets/images/logo-sm.png" alt="small logo" height="22">
							</span>
						</a>

						<!-- Logo Dark -->
						<a href="index.html" class="logo-dark">
							<span class="logo-lg">
								<img src="<?php echo ADMIN_PANEL_URL; ?>assets/images/logo-dark.png" alt="dark logo" height="22">
							</span>
							<span class="logo-sm">
								<img src="<?php echo ADMIN_PANEL_URL; ?>assets/images/logo-dark-sm.png" alt="small logo" height="22">
							</span>
						</a>
					</div>

					<!-- Sidebar Menu Toggle Button -->
					<button class="button-toggle-menu">
						<i class="mdi mdi-menu"></i>
					</button>

					<!-- Horizontal Menu Toggle Button -->
					<button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
						<div class="lines">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</button>

					<ul class="list-unstyled topbar-menu float-end mb-0">
						<li class="notification-list d-none d-sm-inline-block">
							<a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
								<i class="ri-settings-3-line noti-icon"></i>
							</a>
						</li>

						<li class="notification-list d-none d-sm-inline-block">
							<a class="nav-link" href="javascript:void(0)" id="light-dark-mode">
								<i class="ri-moon-line noti-icon"></i>
							</a>
						</li>

						<li class="notification-list d-none d-md-inline-block">
							<a class="nav-link" href="#" data-toggle="fullscreen">
								<i class="ri-fullscreen-line noti-icon"></i>
							</a>
						</li>

						<li class="dropdown notification-list">
							<a class="nav-link dropdown-toggle nav-user arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
								<span class="account-user-avatar">
									<img src="<?php echo ADMIN_PANEL_URL; ?>assets/images/users/avatar-1.jpg" alt="user-image" class="rounded-circle">
								</span>
								<span>
									<span class="account-user-name" style="margin-top: 0.5rem;"><?php echo $logged_in_username; ?></span>
								</span>
							</a>
							<div class="dropdown-menu dropdown-menu-end dropdown-menu-animated topbar-dropdown-menu profile-dropdown">
								<!-- item-->
								<!-- <div class=" dropdown-header noti-title">
								<h6 class="text-overflow m-0">Welcome !</h6>
							</div> -->

								<!-- item-->
								<a href="javascript:void(0);" class="dropdown-item notify-item">
									<i class="mdi mdi-account-circle me-1"></i>
									<span>My Account</span>
								</a>
								<!-- item-->
								<a href="logout.php" class="dropdown-item notify-item">
									<i class="mdi mdi-logout me-1"></i>
									<span>Logout</span>
								</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<!-- ========== Topbar End ========== -->
		<?php } ?>