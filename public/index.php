<?php
session_start();
require_once '../config/Database.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// Simple Router
switch($action) {
    case 'home':
        require_once '../views/home.php';
        break;
    case 'login':
        require_once '../controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
        break;
    case 'register':
        require_once '../controllers/AuthController.php';
        $auth = new AuthController();
        $auth->register();
        break;
    case 'logout':
        require_once '../controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;
    case 'menus':
        require_once '../views/menus.php';
        break;
    case 'book':
        require_once '../controllers/ReservationController.php';
        $resCtrl = new ReservationController();
        $resCtrl->book();
        break;
    case 'payment':
        require_once '../controllers/ReservationController.php';
        $resCtrl = new ReservationController();
        $resCtrl->payment();
        break;
    case 'my_reservations':
        require_once '../controllers/ReservationController.php';
        $resCtrl = new ReservationController();
        $resCtrl->myReservations();
        break;
    case 'cancel_reservation':
        require_once '../controllers/ReservationController.php';
        $resCtrl = new ReservationController();
        $resCtrl->cancelReservation();
        break;
    case 'profile':
        require_once '../controllers/ProfileController.php';
        (new ProfileController())->show();
        break;

    // --- API Endpoints ---
    case 'api_check_availability':
        require_once '../controllers/ReservationController.php';
        $res = new ReservationController();
        $res->apiCheckAvailability();
        break;
    case 'api_get_my_reservation':
        require_once '../controllers/ReservationController.php';
        $res = new ReservationController();
        $res->apiGetMyReservation();
        break;
    case 'api_get_preorder':
        require_once '../controllers/AdminController.php';
        $admin = new AdminController();
        $admin->apiGetPreOrder();
        break;
    case 'api_get_reservation_details':
        require_once '../controllers/AdminController.php';
        $admin = new AdminController();
        $admin->apiGetReservationDetails();
        break;

    // ── Admin Routes ──────────────────────────────────────────────
    case 'admin_dashboard':
        require_once '../controllers/AdminController.php';
        (new AdminController())->dashboard();
        break;

    case 'admin_users':
        require_once '../controllers/AdminController.php';
        (new AdminController())->users();
        break;
    case 'admin_user_create':
        require_once '../controllers/AdminController.php';
        (new AdminController())->userCreate();
        break;
    case 'admin_user_edit':
        require_once '../controllers/AdminController.php';
        (new AdminController())->userEdit();
        break;
    case 'admin_user_delete':
        require_once '../controllers/AdminController.php';
        (new AdminController())->userDelete();
        break;

    case 'admin_tables':
        require_once '../controllers/AdminController.php';
        (new AdminController())->tables();
        break;
    case 'admin_table_create':
        require_once '../controllers/AdminController.php';
        (new AdminController())->tableCreate();
        break;
    case 'admin_table_edit':
        require_once '../controllers/AdminController.php';
        (new AdminController())->tableEdit();
        break;
    case 'admin_table_delete':
        require_once '../controllers/AdminController.php';
        (new AdminController())->tableDelete();
        break;

    case 'admin_menus':
        require_once '../controllers/AdminController.php';
        (new AdminController())->menus();
        break;
    case 'admin_menu_create':
        require_once '../controllers/AdminController.php';
        (new AdminController())->menuCreate();
        break;
    case 'admin_menu_edit':
        require_once '../controllers/AdminController.php';
        (new AdminController())->menuEdit();
        break;
    case 'admin_menu_delete':
        require_once '../controllers/AdminController.php';
        (new AdminController())->menuDelete();
        break;

    case 'admin_reservations':
        require_once '../controllers/AdminController.php';
        (new AdminController())->reservations();
        break;
    case 'admin_reservation_status':
        require_once '../controllers/AdminController.php';
        (new AdminController())->reservationUpdateStatus();
        break;
    case 'admin_process_refund':
        require_once '../controllers/AdminController.php';
        (new AdminController())->processRefund();
        break;
    case 'admin_settle_reservation':
        require_once '../controllers/AdminController.php';
        (new AdminController())->settleReservation();
        break;

    default:
        require_once '../views/home.php';
        break;
}
?>

