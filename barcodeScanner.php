<?php

$conn = mysqli_connect("localhost", "root", "", "barcode");
// Check connection
if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Print host information
echo "Connect Successfully. Host info: " . mysqli_get_host_info($conn);



        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id = $_POST['prodid'];
            $prodname = $_POST['productname'];
            $prodtype = $_POST['producttype'];
            $price = $_POST['price'];
            $loc = "Location: index.php?id=" . $id;
            $sqlinsertproduct = "INSERT INTO product (id, name, type, price) VALUES ('$id', '$prodname', '$prodtype', '$price')";
            mysqli_query($conn,$sqlinsertproduct);
            header($loc);
            exit();
        }

mysqli_close($conn);
?>