<?php
try {
    $host = 'localhost';
    $db = 'checkimmo';
    $user = 'root';
    $pass = 'root';
    $port = "3306";
    $charset = 'utf8mb4';

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }


    $annonces = $pdo->query('select a.id,a.url from annonces a;')->fetchAll();

    foreach ($annonces as $annonce) {
        $prix = getPrix($annonce["url"]);

        $data = [
            'id' => $annonce["id"],
            'prix' => $prix
        ];

        $sqlUpdatePrix = "UPDATE annonces SET prix=:prix where id=:id";
        $sqlInsertSuivi = "INSERT INTO annonce_prix_suivi (idAnnonce, dateSuivi, prix) value (:id, NOW(), :prix);";

        $updatePrix = $pdo->prepare($sqlUpdatePrix);
        $updatePrix->execute($data);

        $insertSuivi = $pdo->prepare($sqlInsertSuivi);
        $insertSuivi->execute($data);
    }

} catch (Exception $e) {
    print "Erreur !: " . $e->getMessage();
    die();
}

function getPrix($urlAnnonce)
{

    $fp = @fopen($urlAnnonce, "r"); // A remplir avec l'url de la page web a aspirer

    $chaine = '';
    $result = '';

    try {
        if ($fp) {
            while (!feof($fp)) {
                $chaine .= fgets($fp, 1024);
            }

            preg_match("/<strong>(.*?) &euro;/", $chaine, $result);

            return str_replace(' ', '', $result[1]);

        } else {
            throw new Exception($urlAnnonce);
        }
    } catch (Exception $e) {
        throw  $e;
    }
}

?>