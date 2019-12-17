<?php
$id = getValueFromArray("id", $_GET, null);
$product = selectProduct($id);
$stock = selectProductStock($id);
$specialdeal = selectSpecialDealByStockItemID($product["StockItemID"]);
$discount = 0;
if(!empty($specialdeal)) {
    $discount = getDiscount($product["RecommendedRetailPrice"], $specialdeal);
}

$customFields = json_decode($product["CustomFields"]);
$tags = json_decode($product["Tags"]);

$description = "";
if(is_array($tags)) {   
    if(count($tags) == 0) {
        $description = "none";
    }

    for ($i = 0; $i < count($tags); $i++) {
        $comma = $i < (count($tags) - 1) ? "," : "";
        $description .= $tags[$i] . $comma;
    }
}

$outputStock = "";
$stockClass = "";
if ($stock["LastStocktakeQuantity"] == 0) {
    $stockClass = 'danger';
    $outputStock = 'Out of stock';
} else if ($stock["LastStocktakeQuantity"] < 100) {
    $stockClass = 'warning';
    $outputStock = 'Almost out of stock!';
}

$images = dbPhoto($product["StockItemID"]);

?>
<div class="container">
    <div class="row">
        <div class="col-sm p-2">
            <div id="productImageCarousel" class="carousel slide">
                <h2><?= $product["StockItemName"] ?></h2>
                <?php if (!empty($specialdeal)) { ?> 
                    <h3 class="text-success"><?= $specialdeal["DealDescription"]; ?></h3>
                <?php } ?>
                <div class="carousel-inner">
                    <?php
                    for ($i = 0; $i < count($images); $i++) { ?>
                        <div class="carousel-item <?= $i == 0 ? 'active' : '' ?>" data-slide-number="<?= $i; ?>">
                            <img class="d-block w-100" src="<?= $images[$i]["Path"] ?>">
                        </div>
                    <?php } ?>
                </div>
                <?php if(count($images) > 1) { ?> 
                    <a class="carousel-control-prev" href="#productImageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#productImageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>           
                    <ol class="carousel-indicators">
                        <?php for ($i = 0; $i < count($images); $i++) { ?>
                            <li data-target="#productImageCarousel" data-slide-to="<?= $i; ?>" class="<?= $i == 0 ? 'active' : ''; ?>">
                                <img class="d-block" src="<?= $images[$i]["Path"]; ?>">
                            </li>
                        <?php } ?>
                    </ol>
                <?php } ?>
            </div>
        </div>
        <div class="col-sm d-flex flex-column align-content-*-end">
            <div>
                <h4><?= $product["MarketingComments"] ?></h4>
                <p><b>Country of manufacture:</b> <?= $customFields->CountryOfManufacture ?? "" ?></p>
                <p><b>Specifications:</b> <?= $description; ?></p>
                <p><b>Lead time days:</b> <?= $product["LeadTimeDays"] ?></p>
                <p><b>Quantity per outer:</b> <?= $product["QuantityPerOuter"] ?></p>
                <p><b>Weight</b> <?= $product["TypicalWeightPerUnit"] ?> kg</p>
                </br>
                <div class="text-<?= $stockClass; ?>"><?= $outputStock ?></div>
            </div>
            <div>
                <?php if(empty($specialdeal)) { ?>
                    <h1> €<?=$product["RecommendedRetailPrice"];?></h1>
                    <h6><?=substr($product["TaxRate"], 0, -1)?>% tax rate included</h6>
                <?php } else { ?>
                    <h2 class="text-danger">
                        <s>€<?=$product["RecommendedRetailPrice"];?></s>
                    </h2>
                    <h1 class="text-success">€<?=$discount;?></h1>
                <?php } ?>
                <br>
                <form method="POST">
                    <input type="hidden" name="productID" value="<?= $product["StockItemID"] ?>" >
                    <div class="row">
                        <div class="col-md-2">  
                            <label for="tbxAmount">Aantal:</label>                          
                            <input type="number" id="tbxAmount" name="amount"  class="form-control" value="1">   
                        </div>
                        <div class="col-md-3 my-3">
                            <button type="submit" name="AddToCart" class="btn btn-success">Add tot Cart</button>
                        </div>
                    </div>
                     </form>
            </div>
        </div>
    </div>
    <div class="row">

    </div>
</div>


<div class="container">
    <div class="row">

        <?php
        $randomProduct = selectProductsByStockGroup($id);
        foreach ($randomProduct as $randProduct){
                $arr = dbPhoto($randProduct["StockItemID"]);
                $specialdeal = selectSpecialDealByStockItemID($randProduct["StockItemID"]);
                if (!empty($specialdeal)) {
                    $discount = getDiscount($randProduct["RecommendedRetailPrice"], $specialdeal);
                }
                echo
                '<div class="col-sm-3">
            <a style="color: black" href="?page=product&action=show&id='.$randProduct["StockItemID"].'">
                <div class="card border-primary bg-light shadow" style="width: auto;">
                    <img class="card-img-top img-fluid" style="height: 190px" src="'.$arr[0]["Path"].'" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title card-title-cap">'.$randProduct["StockItemName"].'</h5>';
                if (empty($specialdeal)) {
                    echo '<h2 class="card-title">€'.$randProduct["RecommendedRetailPrice"].'</h2>';
                }
                else {
                    echo '<div class="d-flex justify-content-between" >
                                <h2 class="text-danger m-0" >
                                    <s > €'.$randProduct["RecommendedRetailPrice"].'</s >
                                </h2 >
                                <h2 class="text-success" > €'.$discount.'</h2 >
                            </div >   ';
                }
                echo '
                    </div>
                    <form method="POST" class=" mb-0">
                        <input type="hidden" name="amount" value="1">
                        <input type="hidden" name="productID" value="'.$randProduct["StockItemID"].'">
                        <button type="submit" name="AddToCart" class="btn btn-success btn-square" style="width: 100%; ">Add to cart</button>
                    </form>
                </div>
            </a>
        </div>';
         }
        ?>
    </div>
</div>

