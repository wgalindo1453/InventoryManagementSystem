<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'load_brand')
	{
		echo fill_brand_list($connect, $_POST['category_id']);
	}

	if($_POST['btn_action'] == 'Add')
	{
		$status = 'active';
		/** 
		$query = "
		INSERT INTO product (category_id, brand_id, product_name, product_description, product_quantity, product_unit, product_base_price, product_tax, product_enter_by, product_status, product_date) 
		VALUES (:category_id, :brand_id, :product_name, :product_description, :product_quantity, :product_unit, :product_base_price, :product_tax, :product_enter_by, :product_status, :product_date)
		";
		**/
		$query = "INSERT INTO advanceSE.serial_numbers
		(equipID,
		brandID,
		sn,
		status)
		VALUES
		(:category_id,
		:brand_id,
		:product_name,
		:product_status)";



		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':category_id'			=>	$_POST['category_id'],
				':brand_id'				=>	$_POST['brand_id'],
				':product_name'			=>	$_POST['product_name'],
				':product_status'		=>	$status,
			
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Added';
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
	
		$query = "
		select snID, equip_type, brand_name, sn, status	from serial_numbers snn, equipment e, brands b 
						where snn.equipID = e.equipID and snn.brandID = b.brandID and  snID = '".$_POST["product_id"]."'";

		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '
		<div class="table-responsive">
			<table class="table table-boredered">
		';
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
			
			$output .= '
			<tr>
				<td>Device Serial Number</td>
				<td>'.$row["sn"].'</td>
			</tr>
			
			<tr>
				<td>Category</td>
				<td>'.$row["equip_type"].'</td>
			</tr>
			<tr>
				<td>Brand</td>
				<td>'.$row["brand_name"].'</td>
			</tr>
			
	
			<tr>
				<td>Status</td>
				<td>'.$status.'</td>
			</tr>
			';
		}
		$output .= '
			</table>
		</div>
		';
		echo $output;
	}
	if($_POST['btn_action'] == 'fetch_single')//<--update
	{
		$query = "
		select snID, equip_type, snn.equipID,brand_name,snn.brandID, sn,status	from serial_numbers snn, equipment e, brands b 
						where snn.equipID = e.equipID and snn.brandID = b.brandID and  snID = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_id'	=>	$_POST["product_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['category_id'] = $row['equipID'];
			$output['brand_id'] = $row['brandID'];
			$output["brand_select_box"] = fill_brand_list($connect, $row["equipID"]);
			$output['product_name'] = $row['sn'];
			$output['product_description'] = $row['sn'];
			$output['product_quantity'] = $row['snID'];
			$output['product_unit'] = $row['snID'];

			$output['product_base_price'] = $row['snID'];
			$output['product_tax'] = $row['snID'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit') 
	{
	
		$query = "
		UPDATE advanceSE.serial_numbers
		set equipID = :category_id,
		brandID = :brand_id,
		sn = :product_name,
		status = :product_status
		WHERE snID = :product_id
		";

		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':category_id'			=>	$_POST['category_id'],
				':brand_id'				=>	$_POST['brand_id'],
				':product_name'			=>	$_POST['product_name'],
				':product_id'			=>	$_POST['product_id'],
				':product_status'		=>	$_POST['status']

			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Details Edited';
		}
	}
	
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'inactive';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';
		}
		
		
		$query = "UPDATE serial_numbers
		SET status = :product_status
		WHERE snID = :product_id";
		
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_status'	=>	$status,
				':product_id'		=>	$_POST["product_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product status change to ' . $status;
		}
	}

	/**
	 * EXAMPLE:
<form name=‘fr’ action=‘redirect(.)php’ method=‘POST’>
<include type=‘hidden’ name=‘var1’ value=‘val1’>
<include type=‘hidden’ name=‘var2’ value=‘val2’>
</form>
<script type=‘text/javascript’>
document.fr.submit();
</script>
	 */
	 if($_POST['btn_action'] == 'upload') 
	 {
		 
		echo '<form id="myForm" action="../fileupload/index.php" method="post">';
		
			foreach ($_POST as $a => $b) {
				echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
			}
		
		echo "</form>
		<script type='text/javascript'>
			document.getElementById('myForm').submit();
		</script>";
	 }
}


?>