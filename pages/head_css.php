<head>
    <meta charset="UTF-8">
    <title>Barangay Information System</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../img/bar.png">

    <!-- bootstrap 3.0.2 -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- font Awesome -->
    <link href="../../css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons -->
    <link href="../../css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link href="../../js/morris/morris-0.4.3.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="../../css/AdminLTE.css" rel="stylesheet" type="text/css" />

    <link href="../../css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="../../css/select2.css" rel="stylesheet" type="text/css" />
    <script src="../../js/jquery-1.12.3.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome 5 (Recommended for fas fa-trash-alt) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Include Tailwind CSS (CDN for fast setup) -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    /* Sidebar gradient */
    .left-side.sidebar-offcanvas {
        background: linear-gradient(135deg, rgb(25, 26, 29), rgb(10, 10, 10));
    }

    /* Welcome text */
    .welcome-text {
        font-size: 30px;
    }

    /* Body font */
    body {
        font-family: 'Times New Roman', Times, serif;
        padding-top: 80px;
    }

    /* Titles */
    h3.panel-title,
    .content-header h1 {
        font-family: 'Times New Roman', Times, serif;
    }

    /* DataTables Styling */
    table.dataTable {
        border-collapse: collapse !important;
        font-family: 'Times New Roman', Times, serif;
        width: 100% !important;
    }

    table.dataTable thead {
        background: linear-gradient(135deg, #2c3e50, #4ca1af);
        color: #fff;
        font-size: 14px;
        text-align: center;
    }

    table.table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    table.table tbody tr:hover {
        background-color: #d6e9f5;
    }

    table.table th, 
    table.table td {
        border: 1px solid #dee2e6;
        text-align: center;
        padding: 8px;
        vertical-align: middle;
    }
    .content-header h1 {
    position: relative;
    display: inline-block;
    padding-bottom: px; /* space below the text */
}
/* Box styling for DataTable container */
.box {
    width: 95%; /* You can adjust this to 90%, 80%, or fixed px like 1200px */
    margin: 15px auto; /* Center it with auto margin left/right */
    padding: 15px; /* Space inside the box */
    background-color: #ffffff; /* White background */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Soft shadow */
}

/* Box header styling */
.box-header {
    border-bottom: 1px solid #dee2e6;
    margin-bottom: 10px;
    padding-bottom: 8px;
}

/* Buttons inside header */
.box-header .btn {
    margin-right: 8px;
}

/* Table inside box */
.box-body {
    margin-top: 10px;
}

.box-body table {
    margin: 0; /* Removes default margins inside */
    width: 100%; /* Make table fit the box */
}


.content-header h1::after {
    content: '';
    display: block;
    height: 10px; /* thickness of the zigzag */
    width: 200px; /* length of the zigzag */
    background: linear-gradient(90deg, #FFD700, #FF8C00); /* gold to orange */
    clip-path: polygon(
        0 50%, 10% 0, 20% 50%, 30% 0, 40% 50%, 50% 0, 
        60% 50%, 70% 0, 80% 50%, 90% 0, 100% 50%
    );
    margin-top: 6px;
}

</style>



</head>
