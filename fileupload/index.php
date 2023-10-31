<?php include 'filesLogic.php';

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <link rel="stylesheet" href="style.css">
    <title>Files Upload and Download</title>
  </head>
  <body>
<!-- clean this up-->
    
    <div class="container">
      <div class="row">
        <form action="index.php" method="post" enctype="multipart/form-data" >
        
          <h3>Upload File</h3>
          <input type="file" name="myfile"> <br>
          <button type="submit" name="save">upload</button>
        </form>
        <form action="downloads.php" method="post">
      <input type="submit" value="Download Files">
    </form>
    <form action="../IMSPHP/product.php" method="post">
      <input type="submit" value="Back">
    </form>

      </div>
    </div>
  </body>
</html>