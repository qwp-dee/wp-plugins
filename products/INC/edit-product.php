<?php wp_enqueue_media(); ?>
<div class="wrap">
<?php
 global $wpdb;
    $products_table = $wpdb->prefix . "products";
   if (isset($_GET['edite_id'])) {
                $pid= $_GET['edite_id'];
                $products = $wpdb->get_results("SELECT id, product_title, product_description, product_price, product_category, product_quantity, product_sku, product_image  from $products_table where id=$pid");        
    }

    //insert product information,
    if (isset($_POST['update'])) {
            $id = $_POST['id'];
            $product_title = $_POST["product_title"];
            $product_description = $_POST["product_description"];
            $product_price = $_POST["product_price"];
            $product_category =  $_POST["product_category"];
            $product_quantity = $_POST["product_quantity"];
            $product_sku = $_POST["product_sku"];
            $product_image = $_POST['product_image'];
            $folder =  content_url()."/wp-products/".$product_image;
            $tmp = isset( $_FILES['product_image']['tmp_name'] ) ? $_FILES['product_image']['tmp_name'] : null;
            move_uploaded_file($tmp, $folder);
            $wpdb->update( $products_table, array('product_title'=>$product_title, 'product_description'=>$product_description, 'product_price'=>$product_price, 'product_category'=>$product_category, 'product_quantity'=>$product_quantity, 'product_sku'=>$product_sku, 'product_image'=>$product_image   ),array('id'=>$id) );
            $url=site_url()."/wp-admin/admin.php?page=products";
            $message ="Product Updated..!";
            
    }

?>
<!-- Product Insert and delete moduls. -->
    <div class="container bg-light">
        <h2 class = "text-dark">Edit Product</h2>
        <div class="col-lg-8 mx:auto p-2">
            <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <br>
          <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

            <div class="form-group mt-2">
                <label>Product name : </label>
                <input type="hidden" name="id" value="<?php if (isset($products[0]->id )) {echo $products[0]->id; } else{ echo ''; }  ?>">
                <input type="text" name="product_title" value="<?php if (isset($products[0]->product_title )) {echo $products[0]->product_title; } else{ echo ''; }  ?>"  class="col-lg-6"/>
            </div>
            <div class="form-group mt-2">
                <label>Product Description:</label>
                <?php 

                    $product_content = isset( $products[0]->product_description ) ? $products[0]->product_description : '';
                    $editor_settings = array(
                                             'editor_height' => 200,
                                             'quicktags' => array( 'buttons' => 'strong,em,del,close,ul,li,ol,block' ),
                                             'media_buttons' => false
                                            );
                wp_editor($product_content, "product_description", $editor_settings) ?>

            </div>
            <div class="form-group mt-2">
                <label>Price:</label>
                <input type="number" name="product_price" value="<?php if (isset($products[0]->product_price )) {echo $products[0]->product_price; } else{ echo ''; }  ?>" class="form-control col-lg-6">
            </div>
            <div class="form-group mt-2">
                <label>Select Category:</label>
                <?php
                    global $wpdb;
                    $category_table = $wpdb->prefix . 'products_category';
                    $categories = $wpdb->get_results("SELECT id, category_name from $category_table");
                if(empty($categories)) :
                ?>
                <a href="<?php echo admin_url('admin.php?page=add-category') ?>">Create New category</a>
                <?php else : ?>
                <select name="product_category" class="form-control col-lg-6">
                    <?php foreach($categories as $category): 
                        if($category->category_name == $products[0]->product_category ) : ?>
                        <option value="<?php echo $category->category_name; ?>" selected><?php echo $category->category_name; ?></option>
                       <?php else: ?>
                        <option value="<?php echo $category->category_name; ?>"><?php echo $category->category_name; ?></option>
                     <?php endif; ?>
                    <?php endforeach; ?>                    
                </select>
                <?php endif ?>
            </div>
              <div class="form-group mt-2">
                <label>Quantity: </label>
                <input type="number" name="product_quantity" value="<?php if (isset($products[0]->product_quantity )) {echo $products[0]->product_quantity; } else{ echo ''; }  ?>" class=" col-lg-6"/>
            </div>
            <div class="form-group mt-2">
                <label>SKU: </label>
                <input type="text" name="product_sku" value="<?php if (isset($products[0]->product_sku )) {echo $products[0]->product_sku; } else{ echo ''; }  ?>" class=" col-lg-6"/>
            </div>
            <div class="form-group mt-2">
                <div class="col-sm-4 mb-4">
                    <img width="250" src="<?php if (isset($products[0]->product_image )) {echo $products[0]->product_image; } else{ echo ''; }  ?>" id="product-img-url">
               </div>
                <div class="col-sm-4 mb-4">
                     <label>Product image:</label>
                     <input id="product-media-url" type="hidden" value="<?php if (isset($products[0]->product_image )) {echo $products[0]->product_image; } else{ echo ''; }  ?>" name="product_image" accept="image/*" />
                     <input id="product-btn" type="button" class="button" value="Edit Image" />
                </div>
            </div>
            <div class="form-group mt-2">
                <button name="update" type="submit" class="btn btn-success" value="Save"><i class="fa fa-save"></i>Update</button>
                <button name="back" type="submit" class="btn btn-info"><i class="fa fa-chevron-circle-left"></i>
                 <a href="<?php echo admin_url('admin.php?page=products'); ?>">BACK </a></button>
            </div>
        </form> 
       </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($){
  // Define a variable productMedia
  var productMedia;
  $('#product-btn').click(function(e) {
    e.preventDefault();
      if (productMedia) {
      productMedia.open();
      return;
    }
    productMedia = wp.media.frames.file_frame = wp.media({
      title: 'Select product',
      button: {
      text: 'Select product image'
    }, multiple: false });

    productMedia.on('select', function() {
      var attachment = productMedia.state().get('selection').first().toJSON();
      $('#product-media-url').val(attachment.url);
      $("#product-img-url").attr("src", attachment.url);
    });
    // Open the upload dialog
    productMedia.open();
  });
});

</script>