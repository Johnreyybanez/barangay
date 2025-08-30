<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get user role
$role = $_SESSION['role'] ?? '';

echo '
<aside class="left-side sidebar-offcanvas">
    <section class="sidebar">
        <ul class="sidebar-menu" style="margin-top:0; position: fixed; height: 100%; overflow-y: auto; z-index: 1000; transition: all 0.3s ease;">
';

if ($role === "Admin") {
    echo '
        <li><a href="../dashboard/dashboard.php"><i class="fa fa-tachometer-alt"></i> <span>Dashboard</span></a></li>
        <li><a href="../officials/officials.php"><i class="fa fa-users-cog"></i> <span>Barangay Officials</span></a></li>
        <li><a href="../resident/resident.php"><i class="fa fa-users"></i> <span>Residents</span></a></li>
        <li><a href="../reports/financial_reports.php"><i class="fa fa-file-invoice-dollar"></i> Financial</a></li>
        <li><a href="../sitio/sitio_purok.php"><i class="fa fa-map-marker-alt"></i> Sitio & Purok</a></li>
        <li><a href="../clearances/clearances.php"><i class="fa fa-id-card-alt"></i> Indigency & Clearance</a></li>
        <li><a href="../blotter/blotter.php"><i class="fa fa-book"></i> Blotter</a></li>
        <li class="treeview">
            <a href="#"><i class="fa fa-cogs"></i> <span>Maintenance</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
                <li><a href="../doc_type/document_types.php"><i class="fa fa-file-alt"></i> Document Type</a></li>
                <li><a href="../service/service_types.php"><i class="fa fa-cogs"></i> Service Types</a></li>
                <li><a href="../clearance_types/clearance.php"><i class="fa fa-file-signature"></i> Clearance Types</a></li>
                <li><a href="../setting/setting.php"><i class="fa fa-cog"></i> Setting</a></li>
            </ul>
        </li>
        <li class="treeview">
            <a href="#"><i class="fa fa-folder"></i> <span>Documents</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
                <li><a href="../assistance/assistance_requests.php"><i class="fa fa-hands-helping"></i> Assistance Requests</a></li>
                <li><a href="../business/business_registration.php"><i class="fa fa-briefcase"></i> Business Registration</a></li>
                <li><a href="../documents/documents.php"><i class="fa fa-file-alt"></i> Document Module</a></li>
                <li><a href="../senior_pwd/senior_pwd.php"><i class="fa fa-heart"></i> Senior Citizen & PWD Services</a></li>
            </ul>
        </li>
        <li><a href="../staff/staff.php"><i class="fa fa-users-cog"></i> <span>Users</span></a></li>';
} elseif ($role === "clerk") {
    echo '
        <li><a href="../resident/resident.php"><i class="fa fa-users"></i> <span>Residents</span></a></li>
        <li><a href="../clearances/clearances.php"><i class="fa fa-id-card"></i> <span>Clearances</span></a></li>';
} elseif ($role === "officials") {
    echo '
        <li><a href="../officials/officials.php"><i class="fa fa-users-cog"></i> <span>Barangay Officials</span></a></li>';
}

echo '
        </ul>
    </section>
</aside>';
?>

<style>
.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    background: #242525ff; /* Dark background for sidebar */
    width: 220px; /* Fixed width for consistency */
}

.sidebar-menu li {
    position: relative;
    width: 100%; /* Ensure full width for list items */
}

.sidebar-menu a {
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between; /* Ensure content and icons align properly */
    padding: 12px 15px;
    text-decoration: none;
    transition: all 0.2s ease; /* Smooth transition for all properties */
    width: 100%; /* Full width for clickable area */
    box-sizing: border-box; /* Prevent padding issues */
}

.sidebar-menu a:hover {
    background: #34495e; /* Hover background color */
    color: #ecf0f1; /* Slightly lighter text on hover */
    transform: scale(1.02); /* Subtle scale effect */
    box-shadow: inset 4px 0 0 #3498db; /* Left border highlight on hover */
}

.sidebar-menu a i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
    flex-shrink: 0; /* Prevent icon from shrinking */
}

.sidebar-menu .treeview-menu {
    list-style: none;
    padding: 0;
    margin: 0;
    background: #34495e; /* Slightly lighter background for submenus */
}

.sidebar-menu .treeview-menu li a {
    padding-left: 30px; /* Indent submenu items */
    font-size: 0.95em; /* Slightly smaller font for submenus */
}

.sidebar-menu .treeview-menu a:hover {
    background: #3d566e; /* Darker hover for submenu */
    color: #ecf0f1;
    transform: scale(1.02);
    box-shadow: inset 4px 0 0 #3498db;
}

.sidebar-menu .pull-right {
    transition: transform 0.2s ease; /* Smooth rotation for angle icon */
}

.sidebar-menu .treeview.active .pull-right {
    transform: rotate(-90deg); /* Rotate angle icon when active */
}

/* Ensure sidebar is scrollable and fixed */
.sidebar {
    height: 100vh;
    overflow-y: auto;
    scrollbar-width: thin; /* Firefox */
    scrollbar-color: #34495e #2c3e50; /* Firefox */
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: #2c3e50;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #34495e;
    border-radius: 3px;
}
</style>