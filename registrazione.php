
<?php
//prova
require("./class.php");
session_start();

if ((isset($_POST["username"]) && isset($_POST["email"])) && (isset($_POST["password1"]) && isset($_POST["password2"]))){
    $ogg = new DatabaseHandler();
    $ogg->Connection("localhost", "root", "", "sala_cinematografica");
    $errors = $ogg->ValidateRegistrationForm($_POST);
    if (!$errors){
        $ogg->AddUser($_POST["username"], $_POST["email"], $_POST["password1"]);
        $_SESSION["username"] = $_POST["username"];
        $_SESSION["email"] = $_POST["email"];
        header("Location: ./index.php");
    }
}

if (isset($_SESSION["username"]) && isset($_SESSION["email"])){
    header("Location: ./index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="index.css">
<title>The Cinema</title>
</head>
<body>
    <div class="altro_main_div">
        <div class="form_page_main_div">
        <div class="accesso_main_div">
            <h1 class="titolo_accesso">Registrazione</h1>
            <form class = "form_type_1" method = "POST">
                <div class="username-div">
                    <input type="text" name="username" class="text-input" placeholder="Username">
                    <p class="username-error"><?php echo isset($errors["username"]) ? $errors["username"] : ""; ?></p>
                </div>
                <div class="email-div">
                    <input type="text" name="email" class="text-input" placeholder="Email">
                    <p class="username-error"><?php echo isset($errors["email"]) ? $errors["email"] : ""; ?></p>
                </div>
                <div class="password-div">
                    <input type="password" name="password1" class="text-input" placeholder="Password">
                    <p class="password1-error"><?php echo isset($errors["password1"]) ? $errors["password1"] : ""; ?></p>
                    </div>
                    <div class="password-div">
                        <input type="password" name="password2" class="text-input" placeholder="Password">
                        <p class="password2-error"><?php echo isset($errors["password2"]) ? $errors["password2"] : ""; ?></p>
                    </div>
                    <input type="submit" class="button" value="Registrati"> 
                </form>
                <div class="messaggio_link_div">
                    <p>Hai un account?  <span><a href="./accesso.php">Accedi</a>, oppure vai nella <span><a href="./index.php">Home</a></span></p>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>