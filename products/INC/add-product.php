<?php wp_enqueue_media(); ?>
<div class="wrap">
<?php
 global $wpdb;
    $table_products = $wpdb->prefix . "products";
    $product_title = isset( $_POST["product_title"] ) ? $_POST["product_title"] : null;
    $product_description = isset( $_POST["product_description"] ) ? $_POST["product_description"] : null;
    $product_price = isset( $_POST["product_price"] ) ? $_POST["product_price"] : null;
    $product_category = isset( $_POST["product_category"] ) ? $_POST["product_category"] : null;
    $product_quantity = isset( $_POST["product_quantity"] ) ? $_POST["product_quantity"] : null;
    $product_sku = isset( $_POST["product_sku"] ) ? $_POST["product_sku"] : null;
    $product_image = isset( $_POST['product_image'] ) ? $_POST['product_image'] : null;
    //insert product information,
    if (isset($_POST['insert'])) {
            $folder =  content_url()."/wp-products/".$product_image;
            $tmp = isset( $_FILES['product_image']['tmp_name'] ) ? $_FILES['product_image']['tmp_name'] : null;
            move_uploaded_file($tmp, $folder);

            $wpdb->insert( $table_products,
                        array('product_title' => $product_title,
                              'product_description' => $product_description,
                              'product_price' => $product_price,
                              'product_category' => $product_category,
                              'product_quantity' => $product_quantity,    
                              'product_sku' => $product_sku,
                              'product_image' => $product_image
                        )
            );
            $message ="New product Added!";
    }
    //Delete product.
    if (isset($_GET['del_id'])) {
            $id = $_GET['del_id'];
            $delete = $wpdb->query($wpdb->prepare("DELETE FROM $table_products WHERE id = %d", $id));
            $message='Product is deleted..<a href='. admin_url('admin.php?page=products'). '>Back</a>';
            $url = site_url()."/wp-admin/admin.php?page=products";
            echo '<script>window.location.reload($url)</script>';
    }

?>
<!-- Product Insert and delete moduls. -->
    <div class="container bg-light">
        <h2 class = "text-dark">Add New Product</h2>
        <div class="col-lg-8 mx:auto p-2">
            <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <br>
          <form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

            <div class="form-group mt-2">
                <label>Product name : </label>
                <input type="text" name="product_title"  class="col-lg-6"/>
            </div>
            <div class="form-group mt-2">
                <label>Product Description:</label>
                <?php $editor_settings = array(
                                             'editor_height' => 200,
                                             'quicktags' => array( 'buttons' => 'strong,em,del,close,ul,li,ol,block' ),
                                             'media_buttons' => false
                                            );
                wp_editor("", "product_description", $editor_settings) ?>

            </div>
            <div class="form-group mt-2">
                <label>Price:</label>
                <input type="number" name="product_price" class="form-control col-lg-6">
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
                    <option value="-1"> -- Select category --</option>
                    <?php foreach($categories as $category): ?>
                    <option value="<?php echo $category->category_name; ?>"><?php echo $category->category_name; ?></option>
                    <?php endforeach; ?>                    
                </select>
                <?php endif ?>
            </div>
              <div class="form-group mt-2">
                <label>Quantity: </label>
                <input type="number" name="product_quantity" class=" col-lg-6"/>
            </div>
            <div class="form-group mt-2">
                <label>SKU: </label>
                <input type="text" name="product_sku" class=" col-lg-6"/>
            </div>
            <div class="form-group mt-2">
                <div class="col-sm-4 mb-4">
                    <img width="250" id="product-img-url">
               </div>
                <div class="col-sm-4 mb-4">
                     <label>Product image:</label>
                     <input id="product-media-url" type="hidden" name="product_image" accept="image/*" value="Product image" />
                     <input id="product-btn" type="button" class="button" value="Upload Image" />
                </div>
            </div>
            <div class="form-group mt-2">
                <button name="insert" type="submit" class="btn btn-success" value="Save"><i class="fa fa-save"></i> SAVE</button>
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