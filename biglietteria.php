<?php

    require("./class.php");
    session_start();
    $ogg = new DatabaseHandler();
    $ogg->Connection("localhost", "root", "", "sala_cinematografica");

    if (!isset($_SESSION["username"]) && !isset($_SESSION["email"])){
        header("Location: ./accesso.php");
    }

    if (isset($_GET["sala"]) && isset($_GET["film"])){
        $the_film = $ogg->GetFilm($_GET["film"], $_GET["sala"]);
        $the_film = mysqli_fetch_assoc($the_film);
    }
    
    if (isset($_POST["quantity"]) && isset($_POST["ticket"])){
        if ($ogg->PrenTik($_POST["quantity"], $_POST["ticket"], $_SESSION["email"])){
            header("Location: ./account.php");
        }
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
        <form class="bigl_main_div" method = "POST">
            <div class="big_titl_cont">
                <a href="./index.php?sala=<?php echo $_SESSION["sala"]; ?>" class="indietro_butt">Indietro</a>
                <h1 class="titolo_accesso">Biglietteria</h1>
            </div>
            <div class="film_big_cont">
                <div class="film_big_cont_1">
                    <div class="film_big_cont_1_1">
                        <p> <span>Titolo:</span>  <?php echo $the_film["titolo"] ?></p>
                        <p> <span>Genere:</span>  <?php echo $the_film["genere"] ?></p>
                        <p> <span>Anno:</span> <?php echo $the_film["anno"] ?></p>
                    </div>
                    <div class="film_big_cont_1_2">
                        <img src="<?php echo $the_film["path_img"] ?>" alt="locandina">
                    </div>
                </div>
                <div class="film_big_cont_2">
                    <div class="film_big_cont_lista">
                        <?php
                            $ris = $ogg->GetDateFilms($_GET["film"], $_GET["sala"]);
                            while ($row = mysqli_fetch_assoc($ris)){
                                echo '<div class="ele_bigl"><p>Data: ' . $row["data_proiezione"] . '</p><p>Prezzo biglietto: '. $row["prezzo_biglietto"] .' EUR</p><input type="radio" id="sel_film" value = '. $row["id_proiezione"] .' name="ticket"></div>';
                            }
                        ?>
                    </div>
                    <div class="film_big_cont_1_2">
                        <div class="film_big_cont_1_2_1">
                            <label for="quantity">Quantità biglietti:  <input type="number" id="quantity" name="quantity" min="1"></label>
                            <input type = "submit" class="buy_button_2" value = "Prenota">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>