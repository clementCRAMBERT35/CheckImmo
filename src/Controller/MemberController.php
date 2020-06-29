<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\RapportType;
use App\Repository\AnnoncesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/member", name="app_member_")
 */
class MemberController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('member/index.html.twig', [
            'controller_name' => 'MemberController',
        ]);
    }

    /**
     * @Route("/rapport", name="rapport")
     */
    public function genererRapport(AnnoncesRepository $annoncesRepository, Request $request)
    {
        $annonce = new Annonces();
        $prixMin["prix_min"] = 0;
        $prixMax["prix_max"] = 0;
        $prixMoy["prix_moy"] = 0;
        $form = $this->createForm(RapportType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $prixMin = $annoncesRepository->getPrixMin(
                $form->get('NombrePieces')->getData()
                , $form->get('SurfaceHabitable')->getData()
                , $form->get('Secteur')->getData()
            );
            $prixMax = $annoncesRepository->getPrixMax(
                $form->get('NombrePieces')->getData()
                , $form->get('SurfaceHabitable')->getData()
                , $form->get('Secteur')->getData()
            );
            $prixMoy = $annoncesRepository->getPrixMoy(
                $form->get('NombrePieces')->getData()
                , $form->get('SurfaceHabitable')->getData()
                , $form->get('Secteur')->getData()
            );

        }

        return $this->render('member/rapport.html.twig', [
            'form' => $form->createView()
            , 'prixMin' => $prixMin
            , 'prixMax' => $prixMax
            , 'prixMoy' => $prixMoy
        ]);
    }

    /**
     * @Route("/graph", name="graph")
     */
    public function graph(AnnoncesRepository $annoncesRepository)
    {
        $encoders = array(new JsonEncoder());
        $normalizers = array(new GetSetMethodNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        foreach ($annoncesRepository->prixParJour() as $val) {
            $data[] = [$val['date'], (int)$val['prixMin'], (int)$val['prixMax'], (int)$val['prixMoy']];
        }

        $dataJSON = $serializer->serialize($data, 'json');

        return $this->render('member/graph.html.twig', array(
                'datas' => $data,
                'data' => $dataJSON,
            )

        );
    }
}
