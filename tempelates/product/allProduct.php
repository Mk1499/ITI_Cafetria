<?php 
require_once './classes/db.php' ; 
$db = new dbManger() ; 
$products = $db->allProduct () ; 
while ($product = $products->fetch()) {
?>


<div class="col-sm-4">
<div class= "product card" >
    <img src="./assets/images/coolCoffe.jpg" class="card-img-top" alt="product image">
    <div class="card-body">
      <h5 class="card-title p-name"><?php echo $product['name'] ?></h5>
      <p class="card-text">  <strong> Price </strong> : <span value = <?php echo $product['price'] ?>  class="p-price"><?php echo $product['price'] ?> </span>EGP</p>
      <button class="btn btn-primary add-product-order">Add to Your Order </button>
    </div>
  </div>
  </div>
<?php } ?>