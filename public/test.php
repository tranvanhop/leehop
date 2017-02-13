<?php
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.

if ($_POST['submit']) {
    $uploaddir = '/var/www/html/tranvanhop/public/images/';
    $uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
    print_r($_FILES['userfile']);
    echo $uploadfile;

    echo '<pre>';
    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        echo "File is valid, and was successfully uploaded.\n";
    } else {
        echo "Possible file upload attack!\n";
    }

    echo 'Here is some more debugging info:';
    print_r($_FILES['userfile']);

    print "</pre>";
}

?>

<html>
<form action="http://tranvanhop/test.php" method="POST">
    <input name="userfile" type="file">
    <input name="submit" type="submit" value="submit">
</form>
</html>
