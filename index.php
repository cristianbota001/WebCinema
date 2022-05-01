
<?php

    require("./class.php");
    session_start();
    $ogg = new DatabaseHandler();
    $ogg->Connection("localhost", "root", "", "sala_cinematografica");

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
    <nav>
        <div class="titolo_cont">
            <img src="video_camera.png" alt="video_camera">
            <a href="./index.php" class="main_titolo">The Cinema</a>
        </div>

        <?php
            if (isset($_GET["sala"])){
                ?>
                    <a href="./index.php" class="indietro_butt">Indietro</a>
                <?php
            }
        ?>

        <?php
            if (!isset($_SESSION["username"]) && !isset($_SESSION["email"])){
        ?>
            <div class="nav_buttons_cont">
                <a href="./accesso.php">Accedi</a>
                <a href="./registrazione.php">Iscriviti</a>
            </div>
        <?php
            } else{
        ?>
            
            <div class="nav_buttons_cont">
                <a href="./account.php" class = "my_acc_but">Il mio account</a>
                <a href="./logout.php">Esci</a>
            </div>

        <?php
            }
        ?>

    </nav>
    <div class="main_div">
        <div class="second_div">
        <?php

            if (isset($_GET["sala"])){

                $_SESSION["sala"] = $_GET["sala"];

                $ris = $ogg->GetAllFilms($_GET["sala"]);
                
                echo "<h1 class = 'sale_titl' >Film della sala ". $_GET["sala"] ."</h1>";
                
                if ($ris->num_rows > 0){
                   
                    echo "<div class = 'films_div'>";
                    while ($row = mysqli_fetch_assoc($ris)){
                        ?>

                        <div class="films_button">
                            <div class="info_film">
                                <p> <span>Titolo:</span>  <?php echo $row["titolo"] ?></p>
                                <p> <span>Genere:</span>  <?php echo $row["genere"] ?></p>
                                <p> <span>Anno:</span> <?php echo $row["anno"] ?></p>
                                <a href="./biglietteria.php?sala=<?php echo $_GET["sala"] ?>&film=<?php echo $row["id_film"] ?>" class = "buy_button">Prenota biglietti</a>
                            </div>
                            <img src="<?php echo $row["path_img"] ?>" alt="locandina">
                        </div>

                        <?php
                    }
                    echo "</div>";
                
                }else{
                    header("Location: ./index.php");
                }
            }else{
                $_SESSION["sala"] = "";
                $ris = $ogg->GetAllSala();
                echo "<h1 class = 'sale_titl' >Sale del cinema</h1>";
                echo "<div class = 'sale_div' >";
                while ($row = mysqli_fetch_assoc($ris)){
                    echo "<a class = 'button_sala' href = './index.php?sala=". $row["id_sala"] ."'>". $row["nome_sala"] ."</a>";
                }
                echo "</div>";
            }

        ?>
        </div>
    </div>
</body>
</html>