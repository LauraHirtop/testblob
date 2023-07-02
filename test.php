<?php
    header("Access-Control-Allow-Origin: *"); // Permite accesul de la orice origine
    header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS"); // Specifica metodele HTTP permise în cererile fetch
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); // Specifica anteturile personalizate permise în cererile fetch
    header("Access-Control-Max-Age: 3600");

    // Stabilirea conexiunii cu baza de date MySQL
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "testpdf";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $metoda = $_SERVER['REQUEST_METHOD'];
    if ($metoda == "POST") { // Verificarea metodei HTTP POST
        if ($_FILES["pdf_file"]["error"] == UPLOAD_ERR_OK) { // Verificarea dacă fișierul a fost încărcat cu succes
            $tmpFilePath = $_FILES["pdf_file"]["tmp_name"]; // Calea către fișierul temporar

            $pdfContent = file_get_contents($tmpFilePath); // Obținerea conținutului fișierului PDF

            $stmt = $conn->prepare("INSERT INTO testpdf (file) VALUES (?)"); // Pregătirea declarației SQL pentru inserarea conținutului fișierului în baza de date
            $stmt->bind_param("s", $pdfContent); // Legarea conținutului fișierului ca parametru

            if ($stmt->execute()) { // Executarea declarației SQL
                echo "Fișier încărcat și inserat în baza de date."; // Afișarea unui mesaj de succes
            } else {
                echo "Eroare la inserarea fișierului în baza de date."; // Afișarea unui mesaj de eroare
            }

            $stmt->close();
        } else {
            echo "Eroare la încărcarea fișierului."; // Afișarea unui mesaj de eroare
        }
    } else if ($metoda == "GET") { // Verificarea metodei HTTP GET
        $sql = "SELECT * FROM testpdf"; // Interogarea pentru a obține fișierele PDF din baza de date
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pdfData = $row['file']; // Obținerea conținutului fișierului din rezultatul interogării
        
                $pdfName = uniqid().".pdf"; // Generarea unui nume unic de fișier
        
                header("Content-Type: application/octet-stream");
                header("Content-Transfer-Encoding: Binary");
                header("Content-Disposition: attachment; filename=\"$pdfName\"");
                
                echo $pdfData; // Afișarea conținutului fișierului
            }
        } else {
            echo "Nu s-au găsit fișiere PDF."; // Afișarea unui mesaj când nu
        }
    }
    
    $conn->close();
    ?>
    