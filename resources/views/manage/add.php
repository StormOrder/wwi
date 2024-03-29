<div class="container">

<?php
$message = false;

if(isset($_SESSION['userName'])){
$peopleInfo  = selectOnePeople($_SESSION['userName']);
if($peopleInfo['IsSalesperson'] == 1 || $peopleInfo['IsSystemUser'] == 1 || $peopleInfo['IsEmployee'] == 1){
    $suppliers = suppliers();
    $categoryID = category();
if(isset($_POST["submit"])){
$stockItemName = $_POST["productname"];
$supplierID = $_POST["supplierID"];
$colorID = 1;
$unitPackageID = 7;
$outerPackageID = 7;
$leadTimeDays = $_POST["leadTimeDays"];
$quantityPerOuter = $_POST["quantityPerOuter"];
$isChillerStock = 0;
$taxRate = $_POST["taxRate"];
$unitPrice = $_POST["unitPrice"];
$typicalWeightPerUnit = $_POST["typicalWeightPerUnit"];
$marketingComments = $_POST["marketingcomments"];
$searchDetails = $_POST["searchDetails"];
$lastEditedBy = selectOnePeople($_SESSION['userName']);
$stock = $_POST["stock"];
$validFrom = "2016-05-31 23:00:00";
$validTo = "9999-12-31 23:59:59";
$category = $_POST["category"];

    $required = array('category','productname', 'supplierID', 'leadTimeDays', 'quantityPerOuter', 'taxRate', 'unitPrice','typicalWeightPerUnit','marketingcomments','searchDetails', 'stock');

// Loop over field names, make sure each one exists and is not empty
    $error = false;
    foreach($required as $field) {
        if (empty($_POST[$field])) {
            $error = true;
        }
    }

    if ($error) {
        echo '<div class="alert alert-danger text-center"><strong>Failed!</strong> All fields are required.</div>';
    } else {
        $recommendedRetailPrice = $unitPrice * ($taxRate/100+1);
        createProduct($stockItemName,$supplierID,$colorID,$unitPackageID, $outerPackageID, $leadTimeDays, $quantityPerOuter,$isChillerStock,$taxRate,$unitPrice,$typicalWeightPerUnit,$marketingComments,$searchDetails,$lastEditedBy['PersonID'],$validFrom, $validTo, $stock, $recommendedRetailPrice, $category);
        $message = true;
    }
} else {
$stockitemname = "";
$supplierID = "";
$unitPackageID = "";
$outerPackageID = "";
$lastEditedBy = "";
$recprice = "";
$marketingcomments = "";
$stock = "";
}


        if ($message == true) {
            echo '<div class="alert alert-success text-center"><strong>Succes!</strong> This product has been added.</div>';
            $message = false;
        }
        ?>

<form class="form-group" method="post" action="?page=manage&action=add">
    Product Name:
    <input type="text" class="form-control" placeholder="Product Name" name="productname" value="<?= isset($_POST['productname']) ? $_POST['productname'] : '' ?>">
    Category:
    <select class="form-control" name="category" value="<?= isset($_POST['category']) ? $_POST['category'] : '' ?>">

        <?php
        foreach ($categoryID as $cat){
            print("<option value='" . $cat["StockGroupID"] . "'>");
            print($cat["StockGroupName"]);
            print("</option>");
        }
        ?>
    </select>

    Supplier ID:
    <select class="form-control" name="supplierID" value="<?= isset($_POST['supplierID']) ? $_POST['supplierID'] : '' ?>">

        <?php
        foreach ($suppliers as $supp){
            print("<option value='" . $supp["SupplierID"] . "'>");
            print($supp["SupplierName"]);
            print("</option>");
        }
        ?>
    </select>
    Lead Time Days:
    <input type="number" class="form-control" placeholder="Lead Time Days" name="leadTimeDays" value="<?= isset($_POST['leadTimeDays']) ? $_POST['leadTimeDays'] : '' ?>"">
    Quantity Per Outer:
    <input type="number" class="form-control" placeholder="Quantity Per Outer" name="quantityPerOuter" value="<?= isset($_POST['quantityPerOuter']) ? $_POST['quantityPerOuter'] : '' ?>"">
    Unit Price:
    <input type="text" class="form-control" placeholder="Unit Price" name="unitPrice" value="<?= isset($_POST['unitPrice']) ? $_POST['unitPrice'] : '' ?>"">
    Tax Rate (in %):
    <input type="number" class="form-control" placeholder="Tax Rate" name="taxRate" value="<?= isset($_POST['taxRate']) ? $_POST['taxRate'] : '' ?>">
    Typical Weight Per Unit (in kg):
    <input type="text" class="form-control" placeholder="Typical Weight Per Unit" name="typicalWeightPerUnit" value="<?= isset($_POST['typicalWeightPerUnit']) ? $_POST['typicalWeightPerUnit'] : '' ?>"">
    Marketing Comments:
    <input type="text" class="form-control" placeholder="Marketing Comments" name="marketingcomments" value="<?= isset($_POST['marketingcomments']) ? $_POST['marketingcomments'] : '' ?>"">
    Search Detail:
    <input type="text" class="form-control" placeholder="Search Detail" name="searchDetails" value="<?= isset($_POST['searchDetails']) ? $_POST['searchDetails'] : '' ?>">
    In Stock:
    <input type="number" class="form-control" placeholder="In Stock" name="stock" value="<?= isset($_POST['stock']) ? $_POST['stock'] : '' ?>">
    <br>
    <input type="submit" class="btn btn-primary" name="submit" value="Submit">

        </form>
        <?php
    }
}
        ?>

</div>