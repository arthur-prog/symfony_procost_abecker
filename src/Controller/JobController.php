<?php
namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Manager\JobManager;
use App\Repository\JobRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class JobController extends AbstractController
{
    public function  __construct(
        private JobManager $jobManager,
        private JobRepository $jobRepository,
    ){
    }

    #[Route('/jobs', name: 'jobs_list', methods: ['GET'])]
    public function jobList(PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $data = $this->jobRepository->findAll();
        $jobs = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('/Jobs/list.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route('/jobs/form/add', name: 'jobs_form_add', methods: ['GET', 'POST'])]
    public function jobFormAdd(Request $request): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->jobManager->save($job);
            return $this->redirectToRoute('jobs_form_add');
        }

        return $this->render('/Jobs/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/jobs/form/update/{id}', name: 'jobs_form_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function jobFormUpdate(Request $request, int $id): Response
    {
        $job = $this->jobRepository->findOneBy(['id' => $id]);

        if($job === null){
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(JobType::class, $job);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->jobManager->update($job);
            return $this->redirectToRoute('jobs_form_update', ['id' => $id]);
        }

        return $this->render('/Jobs/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/jobs/delete/{id}', name: 'jobs_delete', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function jobDelete(int $id): Response
    {
        $job = $this->jobRepository->findOneBy(['id' => $id]);

        if($job === null){
            throw new NotFoundHttpException();
        }

        $this->jobManager->delete($job);

        return $this->redirectToRoute('jobs_list');
    }

}