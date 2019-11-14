<?php

function createConnection() 
{
    //
    // Create an connection with the given information from that ini file.
    //
    $conn = mysqli_connect("localhost", "root", "", "wideworldimporters");

    //
    // If there seems to be an error,
    //
    if (mysqli_connect_errno()) 
    {
        //
        // Show the error and stop loading the website.
        //
        throw new Exception("Connection failed: " . mysqli_connect_error());
    } 

    if(!$conn)
    {
        throw new Exception("Something goes wrong mate!");
    }

    return $conn; 
}

function closeConnection($connection) 
{
    mysqli_close($connection);
}

function selectCustomers()
{
    $connection = createConnection();
    $sql = "SELECT nummer, naam, woonplaats FROM klant WHERE nummer=?";
    closeConnection($connection);
    return mysqli_fetch_all(mysqli_query($connection, $sql), MYSQLI_ASSOC);
}


function selectOneCustomer($conn, $id)
{
    $sql = "SELECT nummer, naam, woonplaats FROM klant WHERE nummer=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function VoegKlantToe($connection, $naam, $woonplaats) {
    $statement = mysqli_prepare($connection, "INSERT INTO klant (naam, woonplaats) VALUES(?,?)");
    mysqli_stmt_bind_param($statement, 'ss', $naam, $woonplaats);
    mysqli_stmt_execute($statement);
    return mysqli_stmt_affected_rows($statement) == 1;
}

function klantBestaat($naam)
{
    return mysqli_query(createConnection(), "SELECT * FROM `klant` WHERE naam=" .$naam);
}

function BewerkKlant($connection, $nummer, $naam, $woonplaats) 
{
    $sql = "UPDATE klant SET naam =?, woonplaats =? WHERE nummer =?";
    $statement = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($statement, 'sss', $naam, $woonplaats, $nummer);
    mysqli_stmt_execute($statement);
    
    return mysqli_stmt_affected_rows($statement) == 1;
}


