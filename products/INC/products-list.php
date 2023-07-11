<div class="wrap">
    <h1 class="text-dark">Product List</h1>
    <div class="container">
        <table class="table table-bordered" id="product_table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Product name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>SKU</th>
                    <th>Image</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody> 
            <?php
            global $wpdb;
                $products_table = $wpdb->prefix . 'products';
                $products = $wpdb->get_results("SELECT id, product_title, product_description, product_price, product_category, product_quantity, product_sku, product_image  from $products_table");
            $i = 1;
            foreach($products as $product ):
            ?> 
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $product->product_title; ?></td>
                <td><?php echo $product->product_description; ?></td>
                <td><?php echo $product->product_price; ?></td>
                <td><?php echo $product->product_category; ?></td>
                <td><?php echo $product->product_quantity; ?></td>
                <td><?php echo $product->product_sku; ?></td>
                <td><img src="<?php echo $product->product_image;?>" class="rounded" width="50"></td>
                <td><a href="<?php echo admin_url('admin.php?page=edit-product&edite_id='.$product->id); ?>" class="btn btn-outline-info"><i class="fa fa-edit"></i>Edit</a></td>
                <td><a href="<?php echo admin_url('admin.php?page=add-product&del_id='.$product->id); ?>" class="btn btn-outline-danger"><i class="fa fa-trash"></i>Delete</a> </td>
            </tr> <?php $i++;  endforeach; ?> </tbody>
        </table>
    </div>
</div>