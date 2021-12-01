<?php 
    session_start();
    include("./common/header.php"); 
?>
<?php
$dbConnection = parse_ini_file("Project.ini");
extract($dbConnection);
$conn = new PDO($dsn, $user, $password);

    extract($_POST);  //id pp
    $errors=[];
    
    //che id no null
    function valiId($id){
        if($id == " " || trim($id) == "" || !isset($id)  ){
          $GLOBALS["errors"]["errorLogId"]="ID is requried";
          }
    };    
    //che pp no null
    function valiPP($pp){
        if($pp == " " || trim($pp) == "" || !isset($pp)  )
          $GLOBALS["errors"]["errorLogPP"]="Password is requried";
    };    
    //get data pp
    
    if(isset($_POST["send"]))
    {
        valiId($id);
        valiPP($pp);

        //pp match?
        if( isset($GLOBALS["errors"])&&isset($pp) )
        {
            //log in right
            $sqlName ="SELECT name, password from student where StudentId =:code Limit 1;";
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pStmtName = $conn->prepare($sqlName);
            $pStmtName->execute(['code'=> $id]);
            $executeN= $pStmtName ->fetch(PDO::FETCH_ASSOC);
            if($executeN)
            {
                if (password_verify($pp, $executeN['password']))
                {
                    $name = $executeN['name'];
                    $_SESSION['userName'] = $name; 
                    $_SESSION["logId"]=$id;
                    header("Location: CourseSelection.php");  //back to last page
                    exit( );               
                }
                else {
                     $GLOBALS["errors"]["errorLogIn"]="Incorrect ID and/or Password";
                }
            }           
        } 
    }
    if(isset($_POST["reset"]))
    {
        $id="";
        $pp="";
    }
?>


<div class="box container" >
    <h3>Log In <?php print_r($name); ?> </h3>

    <span class="error"><?=isset($errors["errorLogIn"]) ? $errors["errorLogIn"] : "" ?></span>
    <form action="Login.php" method="post" >
        <p>You need to <span><a  href="NewUser.php">sing up</a> if you are a new user</span></p>

        <div class="row" width="300px" >
            <p class="col-md-2">Student Id: </p>
            <p class="col-md-10">
                <input type="text" name="id" value="<?php print_r($id); ?>"> 
                <span class="error errorId"><?=isset($errors["errorLogId"]) ? $errors["errorLogId"] : "" ?></span>
            </p>
            <p class="col-md-2">Password: </p>
            <p class="col-md-10">
                <input type="password" name="pp" value="<?php print_r($pp); ?>"> 
                <span class="error errorPP"><?php isset($errors["errorLogPP"]) ? $errors["errorLogPP"] : "" ?></span>
            </p>
            <p class="col-md-2"> </p>
            <p class="col-md-10">
                <input type="submit" name="send" value="Submit" width="50px" class="btn-primary btn">
                <input type="submit" name="reset" value="Reset"  width="50px" class="btn-primary btn ">
            </p>
        </div>
    </form>  
</div>
<?php 
    include('./common/Footer.php'); ?>

