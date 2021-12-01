<?php 
    session_start();
    include("./common/header.php"); 
?>
<?php
extract($_POST);

$dbConnection = parse_ini_file("Project.ini");
extract($dbConnection);
$conn = new PDO($dsn, $user, $password);

function valiid($id){
    if($id == " " || trim($id) == "" || !isset($id)  )
    {
        $GLOBALS["errors"]["errorid"]="ID is requried";
    }
    try {
        $dbConnection = parse_ini_file("Project.ini");
        extract($dbConnection);
        $conn = new PDO($dsn, $user, $password);
        $sql ="SELECT * FROM user where userId=:id ;";
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pStmt = $conn->prepare($sql);
        $pStmt->execute(['id'=>$id]);      
        $row =$pStmt->fetchAll();  //  t/f 。fetch(PDO::FETCH_ASSOC);  只做一個      
        if($row)
        {
            $GLOBALS["errors"]["errorid"]= "A User with this ID already singed up";          
        }
    } catch(PDOException $e) {
        $GLOBALS["errors"]["errorDB"]="connect DB error". $e->getMessage();
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
    valiid($id);
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
            $stmt = $conn->prepare("INSERT INTO user (UserId,Name,Phone,Password)VALUES (:id,:name,:phone,:pp)");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':pp', $hashPP);
            $stmt->execute(); //成功
            echo 'success';
            } catch(PDOException $e) {
              echo $e->getMessage();
          }
        $_SESSION["logId"]=$id;
        $_SESSION["logpp"]=$pp;  
        header("Location: Index.php");
        exit();
    };
}
if(isset($_POST["reset"]))
{
    $id="";
    $pp="";
    $phone="";
    $name="";
}
?>
<style>
    .error{color: red;}
</style>
<div class="showForm box container" >
    <form action="NewUser.php" method="post" >
        <h1>Sing Up </h1>
        <p>All fields are required</p>
        <table>
            <tr>
                <th>User Id</th>
                <td><input type="text" name="id" value="<?php print_r($id); ?>" class="id"></td>
                <td class="error errorId"> <?=isset($errors["errorid"]) ? $errors["errorid"] : "" ?></td>
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
