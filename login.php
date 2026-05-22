<?php
session_start();

$conn = new mysqli("localhost", "root", "", "greenhouse_db");

$error = "";

if(isset($_POST['login'])){

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users 
            WHERE username='$username' 
            AND password='$password'";

    $result = $conn->query($sql);

    if($result->num_rows > 0){

        $_SESSION['username'] = $username;

        header("Location: dashboard.php");

    } else {

        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

<title>Login</title>

<style>

body{
    font-family: Arial;
    background:#f2f2f2;
}

.login-box{

    width:350px;
    margin:100px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.2);
}

h2{
    text-align:center;
    color:green;
}

input{

    width:100%;
    padding:10px;
    margin-top:10px;
}

button{

    width:100%;
    padding:10px;
    margin-top:15px;
    background:green;
    color:white;
    border:none;
    cursor:pointer;
}

.error{

    color:red;
    text-align:center;
    margin-top:10px;
}

</style>

</head>

<body>

<div class="login-box">

<h2>Greenhouse Login</h2>

<form method="POST">

<input type="text" name="username" placeholder="Username" required>

<input type="password" name="password" placeholder="Password" required>

<button type="submit" name="login">LOGIN</button>

</form>

<div class="error">
<?php echo $error; ?>
</div>

</div>

</body>
</html>