<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: ../../login.php");
    exit();
}

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="residents_template.csv"');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');

// Open output stream
$output = fopen('php://output', 'w');

// CSV headers (required fields first, then optional fields)
$headers = [
    'fname',           // First Name (Required)
    'lname',           // Last Name (Required)
    'bdate',           // Birth Date (Required) - Format: YYYY-MM-DD
    'gender',          // Gender (Required) - Male/Female
    'mname',           // Middle Name (Optional)
    'bplace',          // Birth Place (Optional)
    'age',             // Age (Optional - will be calculated from bdate if empty)
    'contact_no',      // Contact Number (Optional)
    'civilstatus',     // Civil Status (Optional) - Single/Married/Divorced/Widowed
    'occupation',      // Occupation (Optional)
    'religion',        // Religion (Optional)
    'pwd',             // PWD Status (Optional) - Yes/No
    'senior_citizen',  // Senior Citizen Status (Optional) - Yes/No
    'sitio',           // Sitio (Optional)
    'purok',           // Purok (Optional)
    'address'          // Address (Optional)
];

// Write headers
fputcsv($output, $headers);

// Sample data rows (updated with more realistic and diverse data)
$sampleData = [
    
    [
        'Maria',          // fname
        'Garcia',         // lname
        '1985-06-22',     // bdate
        'Female',         // gender
        'Lopez',          // mname
        'Quezon City',    // bplace
        '40',             // age
        '09187654321',    // contact_no
        'Married',        // civilstatus
        'Nurse',          // occupation
        'Christian',      // religion
        'No',             // pwd
        'No',             // senior_citizen
        'Sitio Dos',      // sitio
        'Purok 2',        // purok
        '456 Oak Ave, Barangay Talaba' // address
    ],
    [
        'Pedro',          // fname
        'Reyes',          // lname
        '1955-03-10',     // bdate
        'Male',           // gender
        'Mendoza',        // mname
        'Cebu City',      // bplace
        '70',             // age
        '09123456789',    // contact_no
        'Widowed',        // civilstatus
        'Retired',        // occupation
        'Catholic',       // religion
        'Yes',            // pwd
        'Yes',            // senior_citizen
        'Sitio Tres',     // sitio
        'Purok 3',        // purok
        '789 Pine St, Barangay Centro' // address
    ],
    [
        'Anna',           // fname
        'Lim',            // lname
        '2000-12-05',     // bdate
        'Female',         // gender
        'Ramos',          // mname
        'Davao City',     // bplace
        '25',             // age
        '09234567890',    // contact_no
        'Single',         // civilstatus
        'Student',        // occupation
        'Protestant',     // religion
        'No',             // pwd
        'No',             // senior_citizen
        'Sitio Cuatro',   // sitio
        'Purok 4',        // purok
        '101 Birch Rd, Barangay San Isidro' // address
    ]
];

// Write sample data
foreach ($sampleData as $row) {
    fputcsv($output, $row);
}

// Close output stream
fclose($output);
exit();
?>