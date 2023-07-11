<div class="wrap">
    <?php
        global $wpdb;
        $products_category = $wpdb->prefix . "products_category";

            //  Insert New category.
            if (isset($_POST['insert'])) {
                $id = isset( $_POST["id"] ) ? $_POST["id"] : null;
                $category_name = isset($_POST["category_name"]) ? $_POST["category_name"] : null;
                $wpdb->insert($products_category, array('category_name' => $category_name), array('%s') );
                $message="New category Added!";
                
            }
            // Delete category.
            if (isset($_GET['del_id'])) {
                $id = $_GET['del_id'];
                    $delete = $wpdb->query($wpdb->prepare("DELETE FROM $products_category WHERE id = %d", $id));
                    $message='Category deleted.<a href='. admin_url('admin.php?page=add-category'). '>Back</a>';
                    $url = site_url()."/wp-admin/admin.php?page=add-category";
                    echo '<script>window.location.replace($url)</script>';
            }

            // Edit category
            if (isset($_GET['edite_id'])) {
                $i= $_GET['edite_id'];
                $categories = $wpdb->get_results("SELECT id, category_name from $products_category where id=$i");
            }

            // Update category
            if(isset($_POST['update'])){
                $id=$_POST['id'];
                $category_name=$_POST['category_name'];
                $wpdb->update( $products_category, array('category_name'=>$category_name ),array('id'=>$id) );
                $url=site_url()."/wp-admin/admin.php?page=add-category";
                $message="category updated!";
                 echo '<script>location.replace($url)</script>';
            }
    ?>
    <h2 class="text-dark">Category managment</h2>
    <!-- Category managment Insert, Edit and Update. -->
    <div class="metabox-holder">
        <div class="postbox">
            <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
            <br/>
            <form style="width:50%; margin: auto;" method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Category name :</th>
                         <input type="hidden" name="id" value="<?php if (isset($categories[0]->id )) {echo $categories[0]->id; } else{ echo ''; }  ?>">
                        <td><input type="text" name="category_name" class="form-control" placeholder="Add New category" value="<?php if (isset($categories[0]->category_name )) {echo $categories[0]->category_name; } else{ echo ''; }  ?>" /></td>
                    </tr>
                </table>
                <?php if(isset($_GET['edite_id'])): ?>
                <button name="update" type="submit" class="btn btn-outline-primary" value="Update"><i class="fa fa-save"></i> Update</button>
                  <?php else: ?>
                <button name="insert" type="submit" class="btn btn-outline-success" value="Save"><i class="fa fa-save"></i> Save</button>
                 <?php endif; ?>
                 <button name="back" type="submit" class="btn btn-outline-info"><i class="fa fa-chevron-circle-left"></i>
                <a href="<?php echo admin_url('admin.php?page=add-category') ?>">Back</a></button>
            </form>   
            <br>   
        </div>
    </div>  

    <!-- Category Listing. -->
    <div class="container">
        <table class="table table-bordered" id="category_table">
            <thead>
            <tr>
                <th>No</th>
                <th>Category name</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
            global $wpdb;
                $category_table = $wpdb->prefix . 'products_category';
                $categories = $wpdb->get_results("SELECT id, category_name from $category_table");
             $i = 1;
            foreach($categories as $cat ):

            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $cat->category_name; ?></td>
                <td><a href="<?php echo admin_url('admin.php?page=add-category&edite_id=' . $cat->id); ?>" class="btn btn-outline-info" ><i class="fa fa-edit"></i>Update</a></td>
                <td><a href="<?php echo admin_url('admin.php?page=add-category&del_id=' . $cat->id); ?>" class="btn btn-outline-danger"><i class="fa fa-trash"></i>Delete</a></td>
            </tr>
            <?php $i++;  endforeach; ?> 
            </tbody>
        </table>
    </div>
</div>