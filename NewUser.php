<?php 
    session_start();
    include("./common/header.php"); 
?>
<?php
extract($_POST);
//grab value > validate
$errors=[];  // save key and "error string"

$dbConnection = parse_ini_file("Lab5.ini");
extract($dbConnection);
$conn = new PDO($dsn, $user, $password);

function valiid($sid){
    if($sid == " " || trim($sid) == "" || !isset($sid)  )
    {
        $GLOBALS["errors"]["errorSid"]="ID is requried";
    }
     $collSid=[];
        try {
            $dbConnection = parse_ini_file("Lab5.ini");
            extract($dbConnection);
            $conn = new PDO($dsn, $user, $password);
            $sql ="SELECT * FROM `semester` ;";
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully";
            foreach($conn->query('SELECT StudentId FROM student ;') as $row) {    
                array_push($collSid,$row[0]);  
            }   
        } catch(PDOException $e) {
            $GLOBALS["errors"]["errorDB"]="connect DB error". $e->getMessage();
        }
        //match?
        if(count($collSid)>0)
        {
            foreach ($collSid as $value) {
                if($sid == $value)
                    $GLOBALS["errors"]["errorSid"]= "A student with this ID already singed up";             
            }
        }
}

function valiName($name){
    if($name == " " || trim($name) == "" || !isset($name))
      $GLOBALS["errors"]["errorName"]="Name is requried";
};

function valiPhone($phone)
{
    $rulePhone="/([1-9][0-9]{2})(-)([0-9]{3})(-)([0-9]{4})/i";  //([1-9][0-9]{2})([0-9]{3})([0-9]{4})
    $chePhone =preg_match($rulePhone, $phone) ;
    if($chePhone == false || !isset($phone))
            $GLOBALS["errors"]["errorPhone"]="Must be nnn-nnn-nnnn, and ten digits";
};


function valiPP($pp)
{
    $chenumber = preg_match("/[0-9]/i", $pp);
    $cheUppercase = preg_match('@[A-Z]@', $pp);// upper can pass "/[A-Z]/i"
    $ruleLow = '@[a-z]@';
    $cheLowercase = preg_match($ruleLow, $pp); 
    if($cheLowercase == false || $chenumber == false || strlen($pp) <6 || $cheUppercase == false)
    {
        $GLOBALS["errors"]["errorPP"]="Password must be at least 6 characters in length and must contain at least one number, <br> one upper case letter, one lower case letter";
    };
};

function valiPP2($pp2,$pp){
    if( isset($pp) &&$pp2 !== $pp){
        $GLOBALS["errors"]["errorPP2"]="This password is not matched with last one";
    };
};

if($_POST["submit"])
{
    //after btn
    valiid($sid);
    valiName($name);
    valiPhone($phone);
    valiPP($pp);
    valiPP2($pp2, $pp);
    if(count($GLOBALS["errors"]) == 0 || $GLOBALS["errors"] ==[])
    {
        try 
        {
            $hashPP= password_hash($pp , PASSWORD_DEFAULT);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("INSERT INTO student (StudentId,Name,Phone,Password)VALUES (:sid,:name,:phone,:pp)");
            $stmt->bindParam(':sid', $sid);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':pp', $hashPP);
            $stmt->execute(); //成功
            
            } catch(PDOException $e) {
              echo $e->getMessage();
          }
        $_SESSION["logId"]=$sid;
        $_SESSION["logpp"]=$pp;  
        header("Location: CourseSelection.php");
        exit();
    };
}

if(isset($_POST["reset"]))
{
    $sid="";
    $pp="";
    $phone="";
    $name="";
}
?>
<div class="showForm box container" >
    <form action="NewUser.php" method="post" >
        <h1>Sing Up </h1>
        <p>All fields are required</p>
        <table>
            <tr>
                <th>Student Id</th>
                <td><input type="text" name="sid" value="<?php print_r($sid); ?>" class="id"></td>
                <td class="error errorId"> <?=isset($errors["errorSid"]) ? $errors["errorSid"] : "" ?></td>
            </tr>
            <tr>
                <th>Name : </th>
                <td><input type="text" name="name" value="<?php print_r($name); ?>" class="name"></td>
                <td class="error errorName"><?=isset($errors["errorName"]) ? $errors["errorName"] : "" ?></td>
            </tr>            
            <tr>
                <th>Phone Number: <br>(nnn-nnn-nnnn) </th>
                <td><input type="text" name="phone" value="<?php print_r($phone); ?>" class=""></td>
                <td class="error errorPhone"><?=isset($errors["errorPhone"]) ? $errors["errorPhone"] : "" ?></td>
            </tr>
            <tr>
                <th>PassWord :</th>
                <td><input type="password" name="pp" value="<?php print_r($pp); ?>" class=""></td>
                <td class="error errorPP"><span class="error"><?=isset($errors["errorPP"]) ? $errors["errorPP"] : "" ?></span></td>
            </tr>            
            <tr>
                <th>PassWord Again :</th>
                <td><input type="password" name="pp2" value="<?php print_r($pp2); ?>" class=""></td>
                <td class="error errorPP2"><?=isset($errors["errorPP2"]) ? $errors["errorPP2"] : "" ?></td>
            </tr>         
             <tr>
                <th>    </th>
                <td>
                    <input type="submit" name="submit" value="submit" class="btn-primary btn" style="margin-right: 1.5rem ">
                     <input type="submit" name="reset" value="Reset" class="btn-primary btn" >
                </td>
                <td>
               </td>
            </tr>    
        
        </table>
    </form>       
</div>
<?php include('./common/footer.php'); ?>
