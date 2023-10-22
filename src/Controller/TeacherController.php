<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeacherController extends AbstractController
{
    #[Route('/teacher/show/{name}', name: 'show_teacher')]
    public function showTeacher($name): Response
    {
        return $this->render('teacher/showTeacher.html.twig', [
            'name' => $name,
        ]);
    }
    public function goToIndex(): Response
    {
        return $this->redirectToRoute('student_index');
    }

}
