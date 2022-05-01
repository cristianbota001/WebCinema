<?php
    class DatabaseHandler{

        function Connection($server_name, $username, $password, $db){
            $this->server_name = $server_name;
            $this->username =  $username;
            $this->password = $password;
            $this->db = $db;
            
            if ($this->db == NULL){
                $this->conn = mysqli_connect($this->server_name, $this->username, $this->password);
            }else{
                $this->conn = mysqli_connect($this->server_name, $this->username, $this->password, $this->db);
            }
        }

        function CheckIfUserExists($email){
            $ris = mysqli_query($this->conn, "SELECT * FROM utente WHERE utente.email = '$email';");
            if ($ris->num_rows > 0){
                return true;
            }else{
                return false;
            }
        }

        function Authenticate($form){
            $ris = mysqli_query($this->conn, "SELECT * FROM utente WHERE utente.username = '". $form["username"] ."' AND utente.password = '". md5($form["password1"]) ."' AND utente.email = '". $form["email"] ."';");
            if ($ris->num_rows > 0){
                return true;
            }else{
                return false;
            }
        }

        function CheckEmail($email){
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }
        
        function AddUser($username, $email, $password){
            $password = md5($password);
            $ris = mysqli_query($this->conn, "INSERT INTO utente (username, email, password) VALUES ('$username', '$email', '$password')");
        }

        function ValidateRegistrationForm($form){
            $errors = [];
            if ($form["username"]){
                //
            }else{
                $errors["username"] = "Compilare il campo";
            }
            if ($form["email"]){
                if (!$this->CheckEmail($form["email"])){
                    $errors["email"] = "Riscrivere correttamente la email";
                }
                else if ($this->CheckIfUserExists($form["email"])){
                    $errors["email"] = "Email già in utilizzo";
                } 
            }else{
                $errors["email"] = "Compilare il campo";
            }
            if ($form["password1"] && $form["password2"]){
                if ($form["password1"] != $form["password2"]){
                    $errors["password2"] = "Riscrivere correttamente la password";
                }
            }
            if (!$form["password1"]){
                $errors["password1"] = "Compilare il campo";
            }
            if (!$form["password2"]){
                $errors["password2"] = "Compilare il campo";
            }
            return $errors;
        }

        function ValidateLoginForm($form){
            $errors = [];
            if (!$form["username"]){
                $errors["username"] = "Compilare il campo";
            }
            if ($form["email"]){
                if (!$this->CheckEmail($form["email"])){
                    $errors["email"] = "Riscrivere correttamente la email";
                }
            }else{
                $errors["email"] = "Compilare il campo";
            }
            if (!$form["password1"]){
                $errors["password1"] = "Compilare il campo";
            }

            if (!$errors){
                if (!$this->Authenticate($form)){
                    $errors["password1"] = "Utente o password errati";
                }
            }
    
            return $errors;
        }

        function GetAllSala(){
            $ris = mysqli_query($this->conn, "SELECT sala.nome_sala, sala.id_sala FROM sala");
            return $ris;
        }

        function GetAllFilms($sala_id){
            $ris = mysqli_query($this->conn, "SELECT DISTINCT film.titolo, film.genere, film.path_img, film.anno, film.id_film FROM film NATURAL JOIN proiezione NATURAL JOIN sala WHERE sala.id_sala = $sala_id");
            return $ris;
        }

        function GetFilm($film_id, $sala_id){
            $ris = mysqli_query($this->conn, "SELECT DISTINCT film.titolo, film.genere, film.path_img, film.anno, film.id_film FROM film NATURAL JOIN proiezione NATURAL JOIN sala WHERE sala.id_sala = $sala_id AND film.id_film = $film_id");
            return $ris;
        }

        function GetDateFilms($film_id, $sala_id){
            $ris = mysqli_query($this->conn, "SELECT proiezione.data_proiezione, proiezione.prezzo_biglietto, proiezione.id_proiezione FROM film NATURAL JOIN proiezione NATURAL JOIN sala WHERE sala.id_sala = $sala_id AND film.id_film = $film_id");
            return $ris;
        }

        function GetAllPrenTik($username, $email, $pag){
            $ris = mysqli_query($this->conn, "SELECT film.titolo, sala.nome_sala, proiezione.data_proiezione, prenotazione_film.num_biglietti, prenotazione_film.tot_prz, prenotazione_film.id_prenotazione FROM utente NATURAL JOIN prenotazione_film NATURAL JOIN proiezione NATURAL JOIN film NATURAL JOIN sala WHERE utente.username = '$username' AND utente.email = '$email' AND prenotazione_film.pagato = '$pag' ");
            return $ris;
        }

        function GetUtenteFromEmail($email){
            $ris = mysqli_query($this->conn, "SELECT utente.id_utente FROM utente WHERE utente.email = '$email' ");
            return $ris;
        }

        function GetPrzPro($id_proiez){
            $ris = mysqli_query($this->conn, "SELECT proiezione.prezzo_biglietto from proiezione WHERE proiezione.id_proiezione = '$id_proiez' ");
            return $ris;
        }

        function PrenTik($tot_bigl, $id_proiez, $email){
            if ($tot_bigl > 0){
                $id_utente = $this->GetUtenteFromEmail($email);
                $id_utente = mysqli_fetch_assoc($id_utente)["id_utente"];
                $prz_big = $this->GetPrzPro($id_proiez);
                $prz_big = mysqli_fetch_assoc($prz_big)["prezzo_biglietto"];
                $tot = $tot_bigl * $prz_big;
                mysqli_query($this->conn, "INSERT INTO prenotazione_film VALUES(NULL, '$id_proiez', '$id_utente', '$tot_bigl', '$tot', '0')");
                return true;
            }else{
                return false;
            }
            
        }

        function BuyTicket($arr){
            foreach($arr as $ele){
                
                $tot_bigl = mysqli_query($this->conn, "SELECT proiezione.totale_biglietti_disponibili FROM proiezione NATURAL JOIN prenotazione_film WHERE prenotazione_film.id_prenotazione = $ele");
                $tot_bigl = $tot_bigl -> fetch_assoc();
                $num_bigl = mysqli_query($this->conn, "SELECT prenotazione_film.num_biglietti FROM  prenotazione_film WHERE prenotazione_film.id_prenotazione = $ele");
                $num_bigl = $num_bigl -> fetch_assoc();
                
                if (intval($num_bigl["num_biglietti"]) <= intval($tot_bigl["totale_biglietti_disponibili"])){
                    mysqli_query($this->conn, "UPDATE prenotazione_film SET prenotazione_film.pagato = 1 WHERE prenotazione_film.id_prenotazione = $ele");
                    mysqli_query($this->conn, "UPDATE proiezione SET proiezione.totale_biglietti_disponibili = proiezione.totale_biglietti_disponibili - ". intval($num_bigl["num_biglietti"]) ." WHERE proiezione.id_proiezione = (SELECT prenotazione_film.id_proiezione FROM prenotazione_film WHERE prenotazione_film.id_prenotazione = $ele)");
                }else{
                    //gestirlo
                }
            }
        }

    }
?>