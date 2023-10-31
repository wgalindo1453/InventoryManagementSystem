<?php
//call fill_brand_map()




//product_fetch.php

include('database_connection.php');
include('function.php');




function fill_brands($connect){
//create a function to store all brands in key value pair
$query = "select * from brands";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$brand_map = array();
foreach($result as $row)
{
	$brand_map[$row["brandID"]] = $row["brand_name"];
}
//return the brand map
return $brand_map;
}

//store the brand map in a variable
$brand_maps = fill_brands($connect);

$query = '';

$output = array();


/*
$query .= "
	SELECT * FROM product 
INNER JOIN brand ON brand.brand_id = product.brand_id
INNER JOIN category ON category.category_id = product.category_id 
INNER JOIN user_details ON user_details.user_id = product.product_enter_by 
";

EXAMPLE OF CURSOR PAGINATION QUERY:

select snID, sn, equip_type, brand_name 
						from serial_numbers snn, equipment e, brands b 
						where snn.equipID = e.equipID and snn.brandID = b.brandID 
                        and snID > 5 order by snID LIMIT 30 ;
*/
$query .= "
select snID, equip_type, brand_name, sn, status	from serial_numbers snn, equipment e, brands b 
where snn.equipID = e.equipID and snn.brandID = b.brandID ";

//echo $brand_maps[$_POST["search"]["value"]];

if(isset($_POST["search"]["value"]) && !empty($_POST["search"]["value"]))	
{
	// //check if search value is in $brand_map
	 
	 	//query sn to value
	 	$query .= ' and sn = "'.$_POST["search"]["value"].'"';

}


$query .= 'AND snID >  ' . $_POST['start'] . ' ';

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}

else
{
	$query .= 'ORDER BY snID ASC ';
}

/*ORGINAL OFFSET PAGINATION
if($_POST['length'] != -1)
{												
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}
*/

if($_POST['length'] != -1)
{												
	$query .= 'LIMIT '  . $_POST['length'];
}
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
	$status = '';
	
	if($row['status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	
	$sub_array = array();
	$sub_array[] = $row['snID'];
	$sub_array[] = $row['equip_type'];
	$sub_array[] = $row['brand_name'];
	$sub_array[] = $row['sn'];
	$sub_array[] = 1;// available_product_quantity($connect, $row["snID"]);
	$sub_array[] = $row['snID'];
	$sub_array[] = $status;
	$sub_array[] = '<button type="button" name="view" id="'.$row["snID"].'" class="btn btn-info btn-xs view">View</button>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["snID"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["snID"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["snID"].'">Delete</button>';
	$sub_array[] = '<button type="button" name="upload" id="'.$row["snID"].'" class="btn btn-info btn-xs upload">Upload</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	//$statement = $connect->prepare('select MAX(snID) from serial_numbers');
	$statement = $connect->prepare('select max(snID) from serial_numbers');
	$statement->execute();
	//return query result
	//error_log($statement->fetchColumn());
	//echo $statement->fetchColumn();
	//return $statement->fetchColumn();
	//echo $statement->fetchColumn();
	//return $statement->rowCount();
	return $statement->fetchColumn();
}

$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);

echo json_encode($output);

?>