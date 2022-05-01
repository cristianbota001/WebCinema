<?php

    require("./class.php");
    session_start();

    if (!isset($_SESSION["username"]) && !isset($_SESSION["email"])){
        header("Location: ./index.php");
    }

    $ogg = new DatabaseHandler();
    $ogg->Connection("localhost", "root", "", "sala_cinematografica");

    if (isset($_POST["ticket"]) && isset($_FILES["ricevuta"])){
        if ($_FILES["ricevuta"]["size"] > 0){
            $ogg->BuyTicket($_POST["ticket"]);
            move_uploaded_file($_FILES["ricevuta"]["tmp_name"], "./Ricevute/".$_FILES["ricevuta"]["name"]);
        }
    }

    $list_pren_tik = $ogg->GetAllPrenTik($_SESSION["username"], $_SESSION["email"], 0);
    $list_pren_acq = $ogg->GetAllPrenTik($_SESSION["username"], $_SESSION["email"], 1);

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
        <div class="nav_buttons_cont">
            <a href="#" class = "my_acc_but" >Il mio account</a>
            <a href="./logout.php">Esci</a>
        </div>
        <a href="./index.php?sala=<?php echo $_SESSION["sala"]; ?>" class="indietro_butt">Indietro</a>
    </nav>
    <div class="main_div">
        <div class="second_div">
            <?php echo "<h1 class = 'sale_titl' >Benvenuto/a ". $_SESSION["username"] ."</h1>"; ?>
            <div class="account_list_ele">
                <form enctype="multipart/form-data" method="POST" class="div_pren">
                    <h2>Biglietti prenotati</h2>
                    <div class="form_pren">
                        <?php
                            if ($list_pren_tik){
                                while ($row = mysqli_fetch_assoc($list_pren_tik)){
                                    echo '<div class="ele_bigl"><p>Titolo: ' . $row["titolo"] . '</p><p>Sala: '. $row["nome_sala"] .'</p><p>Data: '. $row["data_proiezione"] .'</p><p>Numero biglietti: '. $row["num_biglietti"] .'</p><p>Totale: '. $row["tot_prz"] .' EUR</p><input type="checkbox" value='. $row["id_prenotazione"] .'  id="sel_film" name="ticket[]"></div>';
                                }
                            } 
                        ?>
                    </div>
                    <div class = "acq_sub_div">
                        <input type="submit" class = "button acquista_button" value="Acquista">
                        <input type="file" name = "ricevuta" id = "ricev" style = "display:none;" value="Carica ricevuta">
                        <label for="ricev" class = "button carica_button">Carica ricevuta</label>
                    </div>
                </form>
                <div class="stor_acqu">
                    <h2>Biglietti acquistati</h2>
                    <div class="stor_acqu_list">
                    <?php
                        if ($list_pren_acq){
                            while ($row = mysqli_fetch_assoc($list_pren_acq)){
                                echo '<div class="ele_bigl bac"><p>Titolo: ' . $row["titolo"] . '</p><p>Sala: '. $row["nome_sala"] .'</p><p>Data: '. $row["data_proiezione"] .'</p><p>Numero biglietti: '. $row["num_biglietti"] .'</p><p>Totale: '. $row["tot_prz"] .' EUR</p></div>';
                            }
                        } 
                    ?>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</body>
</html>