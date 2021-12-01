<?php 
    session_start();
    if(!isset($_SESSION["logId"]))
    {
        header("Location: Login.php");
        exit();        
    }else {
        session_destroy();
        header("Location: index.php");
        exit();    }      
    //load header
    include("./common/header.php"); 
?>
<div class="container">	
    <h1>Thank you</h1>
     <?php session_destroy();?>

</div>	
<?php include('./common/footer.php'); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="all.js"></script>
