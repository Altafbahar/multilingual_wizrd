<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IT Form</title>
  <link rel="stylesheet" href="IT_form.css" />
</head>

<body>
    
<h2 class="heading">Well come to the IT department</h2>


<form method="post" class"myform" enctype="multipart/form-data" action="IT_form.php">

    <input type="text" name="txtname" placeholder="Enter your Name">
    <input type="text" name="txtsur" placeholder="Enter your Surname ">
    <input type="text" name="txtemail" placeholder="Enter your Email">
    <input type="text" name="txtphone" placeholder="Enter your Number">
    <input type="file" name="pdf_file" accept="application/pdf" required>
    <input type="submit" value="Submit" name="btnsubmit">
</form>


<?php
if (isset($_POST['btnsubmit'])) {
    $name = $_POST['txtname'];
    $surname = $_POST['txtsur'];
    $email = $_POST['txtemail'];
    $phone = $_POST['txtphone'];

    // Check for uploaded PDF file
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === 0) {
        $fileTmpPath = $_FILES['pdf_file']['tmp_name'];
        $fileName = $_FILES['pdf_file']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileExtension !== 'pdf') {
            die("Only PDF files are allowed.");
        }

        // Folder where files will be saved (relative to this PHP file)
        $uploadFolder = 'uploads/';
        if (!is_dir($uploadFolder)) {
            mkdir($uploadFolder, 0777, true); // create if not exists
        }

        $newFileName = uniqid("pdf_", true) . '.' . $fileExtension;
        $destPath = $uploadFolder . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $pdf_path = $destPath;

            // Connect to database
            $mycon = mysqli_connect("localhost", "root", "", "forms");
            if (!$mycon) {
                die("Database connection failed: " . mysqli_connect_error());
            }

            // Insert record into newsubmit table
            $sql = "INSERT INTO newsubmit (name, surname, email, phone, pdf_path) VALUES (?, ?, ?, ?, ?)";
            $ps = $mycon->prepare($sql);
            $ps->bind_param("sssss", $name, $surname, $email, $phone, $pdf_path);

            if ($ps->execute()) {
                echo "Record inserted and PDF uploaded successfully.";
            } else {
                echo "Error inserting record: " . $ps->error;
            }

            $ps->close();
            $mycon->close();
        } else {
            echo "Failed to move the uploaded file.";
        }
    } else {
        echo "No PDF file uploaded or upload error.";
    }
}
?>
<form action="fees.html" method="post">
    <input type="button" value="pay the fees here">
    <input type="submit" value="submit">
</form>
