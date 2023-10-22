<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/students', name: 'student_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $students = $entityManager->getRepository(Student::class)->findAll();

        return $this->render('student/index.html.twig', [
            'students' => $students,
        ]);
    }
    #[Route('/show', name: 'show')]
    public function show(): Response
    {
        return new Response('<h1>Bonjour Abdennour </h1>');
    }

    #[Route ('/fetch', name: 'fetch')]
    public function fetch(StudentRepository $studentRepository): Response
    {
        $result = $studentRepository->findAll();

        return $this->render('student/test.html.twig',[
            'response' => $result
        ]);
    }
    #[Route ('/add', name: 'add')]
    public function add (ManagerRegistry $managerRegistry):Response {

        $student = new Student();
        $student -> setName('ala');
        $student -> setEmail('ala@gmail.com');
        $student -> setAge(30);

        $entityManager = $managerRegistry->getManager();
        $entityManager -> persist($student);
        $entityManager -> flush();

        return $this->redirectToRoute('fetch');

    }

    #[Route ('/students/create', name: 'create_student')]
    public function create(ManagerRegistry $managerRegistry, Request $request): Response
    {
        // 1: Create a new student instance
        $student = new Student();

        // 2.1: Create the form
        $form = $this->createForm(StudentType::class, $student);

        // 2.2: Handle form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 3: Persist and flush the student entity
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('getAll');
        }

        // Always pass the 'form' variable to the template, even if the form is not submitted
        return $this->render('student/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/getAll',name:'getAll')]
    public function get(StudentRepository $studentRepository){
        $students = $studentRepository->findAll();
        return $this->render('student/showList.html.twig',[
            'students'=>$students
        ]);
    }
    #[Route('/students/delete/{id}',name:'delete')]
    public function delete($id,ManagerRegistry $manager,StudentRepository $studentRepository){
        $student = $studentRepository->find($id);
        $manager->getManager()->remove($student);
        $manager->getManager()->flush();
        return $this->redirectToRoute('getAll');
    }
    #[Route('/students/edit/{id}',name:'update')]
    public function edit(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $student = $entityManager->getRepository(Student::class)->find($id);

        if (!$student) {
            throw $this->createNotFoundException('Aucun étudiant trouvé pour cet ID');
        }

        // Check if the request method is POST (indicating form submission)
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name'); // Replace 'name' with your form field name
            // Handle other form fields similarly

            // Update the student entity with the new data
            $student->setName($name);
            // Update other entity fields as needed

            // Persist the changes to the database
            $entityManager->persist($student);
            $entityManager->flush();

            return $this->redirectToRoute('student_index'); // Redirect after successful update
        }

        return $this->render('student/edit.html.twig', [
            'student' => $student,
        ]);
    }
}
