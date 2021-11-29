<?php
function getPDO()
{
    $dbConnection = parse_ini_file("Project.ini");
    extract($dbConnection);
    $myPdo = new PDO($dsn, $user, $password);
    return $myPdo;
}

