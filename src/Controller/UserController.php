<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('api/users', methods: "POST", name: 'post_user')]

    public function AddUser(Request $request, UserRepository $repo, SerializerInterface $serializer, EntityManagerInterface $manager)
    {
        // $files = $request->request->all();
        // dd($files);
        $doc = $request->files->get("files");
        $file= IOFactory::identify($doc);
        $reader= IOFactory::createReader($file);
        $spreadsheet=$reader->load($doc);
        $fichierexcel= $spreadsheet->getActivesheet()->toArray();
        $result = [];
        $users = [];
        $i = 0;
        foreach($fichierexcel as $data){
            
            $result[$i] = $data;
            $i++;
        }
        unset($result[0]);
        for ($j=1; $j <= count($result); $j++) { 
            # code...
            $users = new User();
            $users->setPrenom($result[$j][0]);
            $users->setNom($result[$j][1]);
            $users->setEmail($result[$j][2]);
            $users->setTelephone($result[$j][3]);
            $users->setAdresse($result[$j][4]);
            $manager->persist($users);
        }
        $manager->flush();
       
         return new JsonResponse("Les utilisateurs ont été ajouté avec succes",Response::HTTP_NOT_FOUND);
}
}