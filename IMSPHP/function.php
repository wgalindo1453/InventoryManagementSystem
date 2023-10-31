<?php 
//function.php

function fill_category_list($connect)
{
    /** 
    $query = "
    SELECT * FROM category 
    WHERE category_status = 'active' 
    ORDER BY category_name ASC
    ";
    **/
    $query = "select * from equipment order by equipID";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output = '';
    foreach($result as $row)
    {
        $output .= '<option value="'.$row["equipID"].'">'.$row["equip_type"].'</option>';
    }
    return $output;
}
//create function to select * from brands order by brandID stores in hash table

//create function to fill search category and brand class="form-group"> in product.php
function fill_category_list_search($connect)
{
    $query = "
    SELECT * FROM equipment"; 
   
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output = '';
    foreach($result as $row)
    {
        $output .= '<option value="'.$row["equipID"].'">'.$row["equip_type"].'</option>';
    }
    return $output;
}
//create function to fill search brand class="form-group"> in product.php
function fill_brand_list_search($connect)
{
    $query = "
    SELECT * FROM brands"; 
   
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output = '';
    foreach($result as $row)
    {
        $output .= '<option value="'.$row["brandID"].'">'.$row["brand_name"].'</option>';
    }
    return $output;
}



function fill_brand_list($connect, $category_id)
{
    /*
    $query = "SELECT * FROM brand
    WHERE brand_status = 'active' 
    AND category_id = '".$category_id."'
    ORDER BY brand_name ASC";
    */


    $query = "select * from brands";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output = '<option value="">Select Brand</option>';
    foreach($result as $row)
    {
        $output .= '<option value="'.$row["brandID"].'">'.$row["brand_name"].'</option>';
    }
    return $output;
}

function get_user_name($connect, $user_id)
{
    $query = "
    SELECT user_name FROM user_details WHERE user_id = '".$user_id."'
    ";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        return $row['user_name'];
    }
}

function fill_product_list($connect)
{
    /*
    $query = "
    SELECT * FROM product 
    WHERE product_status = 'active' 
    ORDER BY product_name ASC
    ";
    */
    $query = "select snID, sn, equip_type, brand_name 
	from serial_numbers snn, equipment e, brands b 
	where snn.equipID = e.equipID and snn.brandID = b.brandID 
	and snn.snID > 0 order by snID LIMIT 30";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output = '';
    foreach($result as $row)
    {
        $output .= '<option value="'.$row["snID"].'">'.$row["sn"].'</option>';
    }
    return $output;
}

function fetch_product_details($product_id, $connect)
{
    $query = "
	select snID,sn, equip_type, brand_name 
	from serial_numbers snn, equipment e, brands b 
	where snn.equipID = e.equipID and snn.brandID = b.brandID and snID  = '".$product_id."' ";
    //WHERE product_id = '".$product_id."'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        $output['product_name'] = $row["sn"];
        $output['quantity'] = $row["snID"];
        $output['price'] = $row['snID'];
        $output['tax'] = $row['snID'];
    }
    return $output;
}

function available_product_quantity($connect, $product_id)
{
    $product_data = fetch_product_details($product_id, $connect);
    /*
    $query = "
    SELECT  inventory_order_product.quantity FROM inventory_order_product 
    INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
    WHERE inventory_order_product.product_id = '".$product_id."' AND
    inventory_order.inventory_order_status = 'active'
    ";
    */
    $query = "select snID,sn, equip_type, brand_name 
	from serial_numbers snn, equipment e, brands b 
	where snn.equipID = e.equipID and snn.brandID = b.brandID and snID = '".$product_id."'";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $total = 0;
    foreach($result as $row)
    {
        $total = $total + $row['snID'];
    }
    //$available_quantity = intval($product_data['snID']) - intval($total);
    $available_quantity = $total;
    if($available_quantity == 0)
    {
        /* 
        $update_query = "
        UPDATE product SET 
        product_status = 'inactive' 
        WHERE product_id = '".$product_id."'
        ";
        */

        $update_query="select max(snID) from serial_numbers";
        $update_query = "";
        $statement = $connect->prepare($update_query);
        $statement->execute();
    }
    //return $available_quantity;
    return 5000297;
}

function count_total_user($connect)
{
    $query = "
    SELECT * FROM user_details WHERE user_status='active'";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

function count_total_category($connect)
{
    /*
    $query = "
    SELECT * FROM category WHERE category_status='active'
    ";
    */
    $query = "select * from equipment";
    $statement = $connect->prepare($query);
    $statement->execute();
    //return result as integer
    
    return $statement->rowCount();
}

function count_total_brand($connect)
{
    /*
    $query = "
    SELECT * FROM brand WHERE brand_status='active'
    ";
    */
    $query = "select * from brands";
    $statement = $connect->prepare($query);
    $statement->execute();
    return $statement->rowCount();
}

function count_total_product($connect)
{
    /*
    $query = "
    SELECT * FROM product WHERE product_status='active'
    ";
    */
    $query = "select MAX(snID) from serial_numbers";
    $statement = $connect->prepare($query);
    $statement->execute();
    //return $statement->rowCount();
    return $statement->fetchColumn();
}

function count_total_order_value($connect)
{
    $query = "
    SELECT sum(inventory_order_total) as total_order_value FROM inventory_order 
    WHERE inventory_order_status='active'
    ";
    if($_SESSION['type'] == 'user')
    {
        $query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
    }
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        return number_format($row['total_order_value'], 2);
    }
}

function count_total_cash_order_value($connect)
{
    $query = "
    SELECT sum(inventory_order_total) as total_order_value FROM inventory_order 
    WHERE payment_status = 'cash' 
    AND inventory_order_status='active'
    ";
    if($_SESSION['type'] == 'user')
    {
        $query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
    }
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        return number_format($row['total_order_value'], 2);
    }
}

function count_total_credit_order_value($connect)
{
    $query = "
    SELECT sum(inventory_order_total) as total_order_value FROM inventory_order WHERE payment_status = 'credit' AND inventory_order_status='active'
    ";
    if($_SESSION['type'] == 'user')
    {
        $query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
    }
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    foreach($result as $row)
    {
        return number_format($row['total_order_value'], 2);
    }
}

function get_user_wise_total_order($connect)
{
    /*
    $query = '
    SELECT sum(inventory_order.inventory_order_total) as order_total, 
    SUM(CASE WHEN inventory_order.payment_status = "cash" THEN inventory_order.inventory_order_total ELSE 0 END) AS cash_order_total, 
    SUM(CASE WHEN inventory_order.payment_status = "credit" THEN inventory_order.inventory_order_total ELSE 0 END) AS credit_order_total, 
    user_details.user_name 
    FROM inventory_order 
    INNER JOIN user_details ON user_details.user_id = inventory_order.user_id 
    WHERE inventory_order.inventory_order_status = "active" GROUP BY inventory_order.user_id
    ';
    */
    $query = "select * from users";
    $statement = $connect->prepare($query);
    $statement->execute();
    $result = $statement->fetchAll();
    $output = '
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <tr>
                <th>User Name</th>
                <th>Total Order Value</th>
                <th>Total Cash Order</th>
                <th>Total Credit Order</th>
            </tr>
    ';

    $total_order = 0;
    $total_cash_order = 0;
    $total_credit_order = 0;
    foreach($result as $row)
    {
        $output .= '
        <tr>
            <td>'.$row["user_name"].'</td>
            <td>'.number_format($row["user_name"], 2).'</td>
            <td>'.number_format($row["user_name"], 2).'</td>
            <td>'.number_format($row["user_name"], 2).'</td>
        </tr>
        ';

        /*
        $output .= '
        <tr>
            <td>'.$row['user_name'].'</td>
            <td align="right">$ '.$row["order_total"].'</td>
            <td align="right">$ '.$row["cash_order_total"].'</td>
            <td align="right">$ '.$row["credit_order_total"].'</td>
        </tr>
        ';
        *
        */

        //$total_order = $total_order + $row["order_total"];
        //$total_cash_order = $total_cash_order + $row["cash_order_total"];
        //$total_credit_order = $total_credit_order + $row["credit_order_total"];
        $total_order = "";
        $total_cash_order = "";
        $total_credit_order = "";
    }
    $output .= '
    <tr>
        <td align="right"><b>Total</b></td>
        <td align="right"><b>$ '.$total_order.'</b></td>
        <td align="right"><b>$ '.$total_cash_order.'</b></td>
        <td align="right"><b>$ '.$total_credit_order.'</b></td>
    </tr></table></div>
    ';
    return $output;
}

?>
