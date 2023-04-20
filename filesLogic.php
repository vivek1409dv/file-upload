<?php //declaring php block

$conn = mysqli_connect('localhost', 'root', '', 'filemanagement');
// connect to data base using mysql iconnect method with host,username,password,database name
$sql = "SELECT * FROM files";
$result = mysqli_query($conn, $sql);
$files = mysqli_fetch_all($result, MYSQLI_ASSOC);
if (isset($_POST['save'])) { // check kro save button click hai
    $filename = $_FILES['myfile']['name'];// name of the uploaded file
//filename lena hai using files array(global array hai) 
    $destination = 'uploads/' . $filename;
//lia hua file name ko destination mein upload karna h which is upload
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
//extension of the file is obtained
    $file = $_FILES['myfile']['tmp_name'];// the physical file on a temporary uploads directory on the server
    $size = $_FILES['myfile']['size'];
//file validation ho raha hai
    if (!in_array($extension, ['zip','jpeg', 'pdf', 'docx','png','jpg'])) {
        echo "You file extension must be .zip .pdf .png .jpeg .jpg or .docx";//validation of file type
    } elseif ($_FILES['myfile']['size'] > 100000000000) { 
        echo "File too large!";//file size ka validation 
    } 
    //if all the validation correct then else part kro file ko destination mein dalo 
    else {
        
        if (move_uploaded_file($file, $destination)) {
            $sql = "INSERT INTO files (name, size, downloads) VALUES ('$filename', $size, 0)";//giving the values in table
            if (mysqli_query($conn, $sql)) {
                header('Location: downloads.php');
            }
        } else {
            echo "Failed to upload file.";//koi error hai toh failed to upload
        }
    }
}
if (isset($_GET['file_id'])) {
    $id = $_GET['file_id'];

    $sql = "SELECT * FROM files WHERE id=$id";
    $result = mysqli_query($conn, $sql);

    $file = mysqli_fetch_assoc($result);
    $filepath = 'uploads/' . $file['name'];

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($filepath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize('uploads/' . $file['name']));
        readfile('uploads/' . $file['name']);//make it downloadable

        $newCount = $file['downloads'] + 1;//increment downloads
        $updateQuery = "UPDATE files SET downloads=$newCount WHERE id=$id";
        mysqli_query($conn, $updateQuery);
        exit;
    }

}