<?php

$url = 'https://www.blot-immobilier.fr/habitat/achat/maison/ille-et-vilaine/saint-armel/fiche/38101/a-vendre-en-exclusivite-chez-blot-immobilier-vern-seiche-4-chambres-t7-beau-terrain-st-armel.html';

$fp = @fopen($url, "r"); // A remplir avec l'url de la page web a aspirer

$chaine = '';
$result = '';
$dep = '';
$img = '';
$site = '';

try {
    if ($fp) {
        while (!feof($fp)) {
            $chaine .= fgets($fp, 1024);
        }
        $chaine = preg_replace('/\s\s+/', ' ', $chaine);

        preg_match_all("/<td>(.*?)<\/td>\s*<td>(.*?)<\/td>/", $chaine, $result);

        if ($result[1][2] != 'Secteur') {
            array_splice($result[1], 2, 0, 'Secteur');
            array_splice($result[2], 2, 0, 'NC');
        }

        if ($result[1][6] != 'Prix FAI') {
            array_splice($result[1], 6, 0, 'Prix FAI');
            array_splice($result[2], 6, 0, '0');
        }

        $result[1][12] = 'Departement';
        preg_match_all("/\/(.+?)\//", $url, $dep);
        $result[2][12] = $dep[0][2];

        $result[1][13] = 'Image';
        preg_match_all('/<img src="(.*?)" alt="" \/>/', $chaine, $img);
        preg_match("/https:\/\/(.*?)\//", $url, $site);
        $result[2][13] = $site[0] . $img[1][1];

        print_r($result);
        cleanInfos($result);

        return $result;
    } else {
        throw new Exception($url);
    }
} catch (Exception $e) {
    throw  $e;
}

function utf8_encode_deep(&$input)
{
    if (is_string($input)) {
        $input = utf8_encode($input);
    } else if (is_array($input)) {
        foreach ($input as &$value) {
            utf8_encode_deep($value);
        }

        unset($value);
    } else if (is_object($input)) {
        $vars = array_keys(get_object_vars($input));

        foreach ($vars as $var) {
            utf8_encode_deep($input->$var);
        }
    }
}

function cleanInfos(&$res)
{
    utf8_encode_deep($res);

// enlever unité
    $res[2][4] = preg_replace("/ m²/", "", $res[2][4]);
    $res[2][9] = preg_replace("/ m²/", "", $res[2][9]);
    $res[2][6] = preg_replace("/&euro;/", "", $res[2][6]);

// NC to vide
    $res[2][9] = preg_replace("/NC/", "", $res[2][9]);
    $res[2][2] = preg_replace("/NC/", "", $res[2][2]);

//espaces
    $res[2][6] = ltrim($res[2][6]);
    $res[2][7] = ltrim($res[2][7]);

// accents
    $res[1][11] = preg_replace("/&Eacute;/", "E", $res[1][11]);

//enlever balise strong
    $res[1][11] = preg_replace("/<strong>|<\/strong>/", "", $res[1][11]);
    $res[2][11] = preg_replace("/<strong>|<\/strong>/", "", $res[2][11]);
}
