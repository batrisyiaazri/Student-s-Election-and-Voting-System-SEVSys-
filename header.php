<html>
<head>
    <title>Database Connection</title>
</head>
<body>
    <?php

    //connect server
    $connect = mysqli_connect("localhost", "root", "", "uptmevs"); //server name, username database, null (pass), name of database

    if (!$connect)
    {
        die ('ERROR:' .mysqli_connect_error());
    }
    ?>

</body>
</html>