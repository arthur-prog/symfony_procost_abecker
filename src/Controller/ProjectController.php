<?php
namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Manager\ProjectManager;
use App\Repository\ProductionTimeRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ProjectController extends AbstractController
{
    public function  __construct(
        private ProjectManager $projectManager,
        private ProjectRepository $projectRepository,
        private ProductionTimeRepository $productionTimeRepository,
    ){
    }

    #[Route('/projects', name: 'projects_list', methods: ['GET'])]
    public function projectsList(PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $data = $this->projectRepository->findAll();
        $projects = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('/Projects/list.html.twig', [
            'projects' => $projects,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/projects/{id}/details', name: 'projects_details', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function projectDetails(Request $request, int $id, PaginatorInterface $paginatorInterface): Response
    {
        $project = $this->projectRepository->findOneById($id);

        if($project === null){
            throw new NotFoundHttpException();
        }

        //production times list
        $data = $this->productionTimeRepository->findByProject($project);
        $productionTimes = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        //stats
        $totalCost = 0;
        $employees = [];

        foreach ($data as $productionTime){

            if(!in_array($productionTime->getEmployee(), $employees))
                $employees[] = $productionTime->getEmployee();

            $totalCost += $productionTime->getNbDays() * $productionTime->getEmployee()->getDailyCost();
        }

        $totalEmployees = sizeof($employees);

        return $this->render('/Projects/detail.html.twig', [
            "productionTimes" => $productionTimes,
            'project' => $project,
            'totalCost' => $totalCost,
            'totalEmployees' => $totalEmployees,
        ]);
    }

    #[Route('/projects/form/add', name: 'projects_form_add', methods: ['GET', 'POST'])]
    public function projectFormAdd(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->projectManager->save($project);
            return $this->redirectToRoute('projects_form_add');
        }

        return $this->render('/Projects/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projects/form/update/{id}', name: 'projects_form_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function projectFormUpdate(Request $request, int $id): Response
    {
        $project = $this->projectRepository->findOneBy(['id' => $id]);

        if($project === null){
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ProjectType::class, $project);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->projectManager->update($project);
            return $this->redirectToRoute('projects_form_update', ['id' => $id]);
        }

        return $this->render('/Projects/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/projects/delete/{id}', name: 'projects_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function projectDelete(int $id): Response
    {
        $project = $this->projectRepository->findOneBy(['id' => $id]);

        if($project === null){
            throw new NotFoundHttpException();
        }

        $this->projectManager->delete($project);

        return $this->redirectToRoute('projects_list');
    }

    #[Route('/projects/deliver/{id}', name: 'projects_deliver', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function projectDeliver(int $id): Response
    {
        $project = $this->projectRepository->findOneBy(['id' => $id]);

        if($project === null){
            throw new NotFoundHttpException();
        }

        $this->projectManager->deliver($project);

        return $this->redirectToRoute('projects_list');
    }

}