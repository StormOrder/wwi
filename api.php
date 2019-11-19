<?php
require_once "./functions/core.php";
require_once "./functions/databaseFunctions.php";

if(isset($_GET["query"]))
{
    //stockitems
    //categories
    if(is_numeric($_GET["query"]))
    {
        $result = selectProductById($_GET["query"]);
    }
    else
    {
        $result = selectProductsLike($_GET["query"]);
    }
    $arr = [];
    foreach($result as $row)
    {
        array_push($arr, $row["StockItemName"]);
    }

    echo json_encode($arr);
}
else {
    echo json_encode([]);
}