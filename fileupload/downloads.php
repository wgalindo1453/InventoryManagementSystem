

<?php error_reporting(E_ERROR | E_PARSE);?>
<?php include 'filesLogic.php';?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="style.css">
  <title>Download files</title>
</head>
<body>

<table>
<thead>
    <th>ID</th>
    <th>Filename</th>
    <th>size (in mb)</th>
    <th>Downloads</th>
    <th>Action</th>
</thead>
<tbody>
  <?php foreach ($files as $file): ?>
    
    
    <tr>
      <td><?php echo $file['id']; ?></td>
      <td><?php echo $file['name']; ?></td>
      <td><?php echo floor($file['size'] / 1000) . ' KB'; ?></td>
      <td><?php echo $file['downloads']; ?></td>
      <td><a href="downloads.php?file_id=<?php echo $file['id'] ?>">Download</a>
      <form action="downloads.php" method="post">
          <input type="hidden" name="id" value="<?php echo $file['id']; ?>">
          <input type="submit" name="delete" value="Delete">
        </form>
    
    </td>
    

      
    </tr>
   

  <?php endforeach;?>
 <!--add style -->
</tbody>
</table>
<form action="../IMSPHP/product.php" method="post">
  <input type="submit" value="Back">
</form>

  

</body>
</html>