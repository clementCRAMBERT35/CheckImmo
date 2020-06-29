<?php

namespace App\Entity;

use App\Repository\AnnoncesRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use function App\aspirerSite;
use function App\utf8_encode_deep;

/**
 * @ORM\Entity(repositoryClass=AnnoncesRepository::class)
 */
class Annonces
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $TypeBien;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Chauffage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Departement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Ville;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Secteur;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $AnneConstruction;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Prix;

    /**
     * @ORM\Column(type="integer")
     */
    private $SurfaceHabitable;

    /**
     * @ORM\Column(type="integer")
     */
    private $NombrePieces;

    /**
     * @ORM\Column(type="integer")
     */
    private $NombreChambres;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $SurfaceSejour;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Etage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Reference;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToMany(targetEntity=Users::class, inversedBy="annonces")
     */
    private $User;

    /**
     * @ORM\Column(type="date")
     */
    private $dateAspiration;

    public function __construct()
    {
        $this->User = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeBien(): ?string
    {
        return $this->TypeBien;
    }

    public function setTypeBien(string $TypeBien): self
    {
        $this->TypeBien = $TypeBien;

        return $this;
    }

    public function getDepartement(): ?string
    {
        return $this->Departement;
    }

    public function setDepartement(string $Departement): self
    {
        $this->Departement = $Departement;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->Ville;
    }

    public function setVille(string $Ville): self
    {
        $this->Ville = $Ville;

        return $this;
    }

    public function getSecteur(): ?string
    {
        return $this->Secteur;
    }

    public function setSecteur(string $Secteur): self
    {
        $this->Secteur = $Secteur;

        return $this;
    }

    public function getAnneConstruction(): ?int
    {
        return $this->AnneConstruction;
    }

    public function setAnneConstruction(?int $AnneConstruction): self
    {
        $this->AnneConstruction = $AnneConstruction;

        return $this;
    }

    public function getSurfaceHabitable(): ?int
    {
        return $this->SurfaceHabitable;
    }

    public function setSurfaceHabitable(int $SurfaceHabitable): self
    {
        $this->SurfaceHabitable = $SurfaceHabitable;

        return $this;
    }

    public function getNombrePieces(): ?int
    {
        return $this->NombrePieces;
    }

    public function setNombrePieces(int $NombrePieces): self
    {
        $this->NombrePieces = $NombrePieces;

        return $this;
    }

    public function getNombreChambres(): ?int
    {
        return $this->NombreChambres;
    }

    public function setNombreChambres(int $NombreChambres): self
    {
        $this->NombreChambres = $NombreChambres;

        return $this;
    }

    public function getSurfaceSejour(): ?int
    {
        return $this->SurfaceSejour;
    }

    public function setSurfaceSejour(?int $SurfaceSejour): self
    {
        $this->SurfaceSejour = $SurfaceSejour;

        return $this;
    }

    public function getEtage(): ?string
    {
        return $this->Etage;
    }

    public function setEtage(string $Etage): self
    {
        $this->Etage = $Etage;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function setAnnonceWithURl($url)
    {
        try {
            $data = $this->aspirerSite($url);

            $this->setTypeBien($data[2][0]);
            $this->setVille($data[2][1]);
            $this->setSecteur($data[2][2]);
            if (intval($data[2][3]) == 0)
                $this->setAnneConstruction(null);
            else
                $this->setAnneConstruction(intval($data[2][3]));
            $this->setSurfaceHabitable(intval($data[2][4]));
            $this->setNombrePieces(intval($data[2][5]));
            if (intval($data[2][6]) == 0)
                $this->setPrix(null);
            else
                $this->setPrix(intval($data[2][6]));
            $this->setChauffage($data[2][7]);
            $this->setNombreChambres(intval($data[2][8]));
            if (intval($data[2][9]) == 0)
                $this->setSurfaceSejour(null);
            else
                $this->setSurfaceSejour(intval($data[2][9]));
            $this->setEtage($data[2][10]);
            $this->setReference($data[2][11]);
            $this->setDepartement($data[2][12]);
            $this->setDateAspiration(new DateTime());
            $this->setUrl($url);


            $img = '../public/annonces/' . substr($this->getReference(), 0, strlen($this->getReference()) - 1) . '.png';

            // Enregistrer l'image
            file_put_contents($img, file_get_contents($data[2][13]));


        } catch (Exception $e) {
            throw new Exception("Erreur dans l'importation de l'annonce : " . $e->getMessage());
        }
    }

    function aspirerSite($url)
    {
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
                    array_splice($result[2], 6, 0, 'NC');
                }

                $result[1][12] = 'Departement';
                preg_match_all("/\/(.+?)\//", $url, $dep);
                $result[2][12] = $dep[0][2];

                $result[1][13] = 'Image';
                preg_match_all('/<img src="(.*?)" alt="" \/>/', $chaine, $img);
                preg_match("/https:\/\/(.*?)\//", $url, $site);
                $result[2][13] = $site[0] . $img[1][1];

                $this->cleanInfos($result);
                return $result;
            } else {
                throw new Exception($url);
            }
        } catch (Exception $e) {
            throw  $e;
        }
    }

    function cleanInfos(&$res)
    {
        $this->utf8_encode_deep($res);

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
        $res[1][10] = preg_replace("/&Eacute;/", "E", $res[1][10]);

        //enlever balise strong
        $res[1][11] = preg_replace("/<strong>|<\/strong>/", "", $res[1][11]);
        $res[2][11] = preg_replace("/<strong>|<\/strong>/", "", $res[2][11]);
    }

    function utf8_encode_deep(&$input)
    {
        if (is_string($input)) {
            $input = utf8_encode($input);
        } else if (is_array($input)) {
            foreach ($input as &$value) {
                $this->utf8_encode_deep($value);
            }

            unset($value);
        } else if (is_object($input)) {
            $vars = array_keys(get_object_vars($input));

            foreach ($vars as $var) {
                $this->utf8_encode_deep($input->$var);
            }
        }
    }

    public function getReference(): ?string
    {
        return $this->Reference;
    }

    public function setReference(string $Reference): self
    {
        $this->Reference = $Reference;

        return $this;
    }

    /**
     * @return Collection|Users[]
     */
    public function getUser(): Collection
    {
        return $this->User;
    }

    public function addUser(Users $user): self
    {
        if (!$this->User->contains($user)) {
            $this->User[] = $user;
        }

        return $this;
    }

    public function removeUser(Users $user): self
    {
        if ($this->User->contains($user)) {
            $this->User->removeElement($user);
        }

        return $this;
    }

    public function getDateAspiration(): ?DateTimeInterface
    {
        return $this->dateAspiration;
    }

    public function setDateAspiration(DateTimeInterface $dateAspiration): self
    {
        $this->dateAspiration = $dateAspiration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChauffage()
    {
        return $this->Chauffage;
    }


    //Utils

    /**
     * @param mixed $Chauffage
     */
    public function setChauffage($Chauffage): void
    {
        $this->Chauffage = $Chauffage;
    }

    /**
     * @return mixed
     */
    public function getPrix()
    {
        return $this->Prix;
    }

    /**
     * @param mixed $Prix
     */
    public function setPrix($Prix): void
    {
        $this->Prix = $Prix;
    }
}