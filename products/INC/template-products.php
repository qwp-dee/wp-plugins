<?php    
   global $wpdb;
   $allproducts = $wpdb->get_results($wpdb->prepare("SELECT * from ".product_table()." ORDER BY id desc ",""));
?>
<div class="wrap">
      <div class="container-lg bg-light">
        <div class="row">
           <?php 
            if(count($allproducts) > 0) :
                foreach($allproducts as $product):
            ?>
          <div class="col-sm-4">
              <div class="card">
                <img class="card-img-top" src="<?php echo $product->product_image; ?>" alt="Card image">
                <div class="card-overlay">
                  <h4 class="card-title"><?php echo $product->product_title; ?></h4>
                    <p class="card-text"><?php echo $product->product_description; ?></p>
                    <ol>
                      <li><span><i>Price :</i></span> <?php echo $product->product_price; ?></li>
                      <li><span><i>Cate:</i></span><?php echo $product->product_category; ?></li>
                      <li><span><i>QTY :</i></span><?php echo $product->product_quantity; ?></li>
                      <li><span><i>SKU :</i></span><?php echo $product->product_sku; ?></li>
                    </ol>
                </div>
               </div>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
</div>