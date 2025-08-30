<?php
// Include the database connection
include '../connection.php';  // Make sure this path is correct

// Check if connection is established
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get clearance_id from the URL
if (isset($_GET['clearance_id'])) {
    $clearance_id = $_GET['clearance_id'];

    // Fetch the clearance data from the database
    $query = "SELECT bc.clearance_id, 
                     CONCAT(r.fname, ' ', r.lname) AS resident_name, 
                     ct.type_name AS clearance_type, 
                     CONCAT(o.sPosition, ' ', o.completeName) AS issued_by, 
                     bc.issue_date, 
                     bc.document_path 
              FROM barangay_clearances bc
              JOIN tblresident r ON bc.resident_id = r.id
              JOIN clearance_types ct ON bc.clearance_type_id = ct.clearance_type_id
              JOIN tblofficial o ON bc.issued_by = o.id
              WHERE bc.clearance_id = ?";

    // Prepare and execute the query
    if ($stmt = $con->prepare($query)) {
        $stmt->bind_param('i', $clearance_id);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($clearance_id, $resident_name, $clearance_type, $issued_by, $issue_date, $document_path);

        if ($stmt->fetch()) {
            // HTML structure with styling for displaying the clearance details
            echo "
            <style>
                body { font-family: Arial, sans-serif; }
                .container { width: 700px; margin: auto; padding: 20px; border: 2px solid white; }
                .header { display: flex; align-items: center; justify-content: center; text-align: center; padding: 10px; }
                .header img { width: 50px; height: 50px; }
                .header h2, .header h3 { margin: 0; }
                .content { margin-top: 20px; display: flex; }
                .left-column, .right-column { padding: 20px; }
                .left-column { width: 30%; }
                .right-column { width: 70%; }
                .footer { margin-top: 70px; text-align: center; }
            </style>
            
           <div class='container'>
                <div class='header'>
                    <img src='../../img/logo.png' style='width: 20%; height: 80px;' /> <!-- Replace with your actual logo path -->
                    <div>
                        <h2>Republic of the Philippines</h2>
                        <h3>Province of Batangas</h3>
                        <h3>CITY OF TANAUAN</h3>
                        <h3>Barangay San Jose</h3>
                    </div>
                </div>
                <hr>

                <h2 style='text-align: center;'>BARANGAY CLEARANCE</h2>
                
                <div class='content'>
                    <!-- Left Column: Official Information -->
                    <div class='left-column' style='background: white; border: 1px solid black;'>
                        <center><img src='../../img/logo.png' style='width: 50%; height: 50px;' /></center>
                        <div style='margin-top: 20px; text-align: center; word-wrap: break-word;'>";

                        // Query to fetch officials and their positions
                        $qry = mysqli_query($con, "SELECT * FROM tblofficial");
                        while ($row = mysqli_fetch_array($qry)) {
                            if ($row['sPosition'] == "Captain") {
                                echo "
                                <p>
                                <b>" . strtoupper($row['completeName']) . "</b><br>
                                <span style='font-size:12px;'>PUNONG BARANGAY</span>
                                </p>";
                            } elseif ($row['sPosition'] == "Kagawad(Ordinance)") {
                                echo "
                                <p>
                                KAG. " . strtoupper($row['completeName']) . "<br>
                                <span style='font-size:12px;'>Sports / Law / Ordinance</span>
                                </p>";
                            } elseif ($row['sPosition'] == "Kagawad(Public Safety)") {
                                echo "
                                <p>
                                KAG. " . strtoupper($row['completeName']) . "<br>
                                <span style='font-size:12px;'>Public Safety / Peace and Order</span>
                                </p>";
                            } elseif ($row['sPosition'] == "Kagawad(Tourism)") {
                                echo "
                                <p>
                                KAG. " . strtoupper($row['completeName']) . "<br>
                                <span style='font-size:12px;'>Culture & Arts / Tourism / Womens Sector</span>
                                </p>";
                            } elseif ($row['sPosition'] == "Kagawad(Budget & Finance)") {
                                echo "
                                <p>
                                KAG. " . strtoupper($row['completeName']) . "<br>
                                <span style='font-size:12px;'>Budget & Finance / Electrification</span>
                                </p>";
                            } elseif ($row['sPosition'] == "Kagawad(Agriculture)") {
                                echo "
                                <p>
                                KAG. " . strtoupper($row['completeName']) . "<br>
                                <span style='font-size:12px;'>Agriculture / Livelihood / Farmers Sector / PWD Sector</span>
                                </p>";
                            } elseif ($row['sPosition'] == "Kagawad(Education)") {
                                echo "
                                <p>
                                KAG. " . strtoupper($row['completeName']) . "<br>
                                <span style='font-size:12px;'>Health & Sanitation / Education</span>
                                </p>";
                            } elseif ($row['sPosition'] == "Kagawad(Infrastructure)") {
                                echo "
                                <p>
                                KAG. " . strtoupper($row['completeName']) . "<br>
                                <span style='font-size:12px;'>Infrastructure / Labor Sector / Environment / Beautification</span>
                                </p>";
                            }
                        }

            echo "
                    </div>
                </div>
                
                <!-- Right Column: Resident Information and Clearance -->
                <div class='right-column' style='background: white;'>
                    <p>To Whom It May Concern:</p>
                    <p>This is to certify that <b>" . $resident_name . "</b>, Filipino, of legal age, is a resident of Barangay San Jose, Tanauan City, Batangas.</p>
                    <p>This further certifies that he/she is a person of good moral character, law-abiding citizen, and has never been convicted of any crime involving moral turpitude nor been a member of any subversive organization which seeks to overthrow our government.</p>
                    <p>Issued this <b>" . $issue_date . "</b> upon request of the above-named for whatever legal purpose it may serve.</p>
                </div>
            </div>
            
            <div class='footer'>
                <p><b>" . strtoupper($issued_by) . "</b><br>Issued By</p>
            </div>

            <script>
                window.onload = function() {
                    window.print();  // Automatically triggers the print dialog when the page loads
                };
            </script>

        </div>";

        } else {
            echo "<p>No clearance found for this ID.</p>";
        }

        $stmt->close();
    }
} else {
    echo "<p>Missing clearance_id.</p>";
}
?>
