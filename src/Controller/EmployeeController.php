<?php
namespace App\Controller;

use App\Entity\Employee;
use App\Entity\ProductionTime;
use App\Form\EmployeeType;
use App\Form\ProductionTimeType;
use App\Manager\EmployeeManager;
use App\Manager\ProductionTimeManager;
use App\Repository\EmployeeRepository;
use App\Repository\ProductionTimeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class EmployeeController extends AbstractController
{
    public function  __construct(
        private EmployeeManager $employeeManager,
        private EmployeeRepository $employeeRepository,
        private ProductionTimeManager $productionTimeManager,
        private ProductionTimeRepository $productionTimeRepository,
    ){
    }

    #[Route('/employees', name: 'employees_list', methods: ['GET'])]
    public function employeesList(PaginatorInterface $paginatorInterface, Request $request): Response
    {
        $data = $this->employeeRepository->findAll();
        $employees = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('/Employees/list.html.twig', [
            'employees' => $employees,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     */
    #[Route('/employees/{id}/details', name: 'employees_details', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function employeeDetails(Request $request, int $id, PaginatorInterface $paginatorInterface): Response
    {
        $employee = $this->employeeRepository->findOneById($id);

        if($employee === null){
            throw new NotFoundHttpException();
        }

        //form production time
        $productionTime = new ProductionTime();
        $productionTime->setEmployee($employee);
        $form = $this->createForm(ProductionTimeType::class, $productionTime);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->productionTimeManager->save($productionTime);
            return $this->redirectToRoute('employees_details', ['id' => $id, 'page' => 1]);
        }


        //production times list
        $data = $this->productionTimeRepository->findByEmployee($employee);
        $productionTimes = $paginatorInterface->paginate(
            $data,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('/Employees/detail.html.twig', [
            "productionTimes" => $productionTimes,
            'employee' => $employee,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/employees/form/add', name: 'employees_form_add', methods: ['GET', 'POST'])]
    public function employeeFormAdd(Request $request): Response
    {
        $employee = new Employee();
        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->employeeManager->save($employee);
            return $this->redirectToRoute('employees_form_add');
        }

        return $this->render('/Employees/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/employees/form/update/{id}', name: 'employees_form_update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function employeeFormUpdate(Request $request, int $id): Response
    {
        $employee = $this->employeeRepository->findOneBy(['id' => $id]);

        if($employee === null){
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(EmployeeType::class, $employee);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->employeeManager->update($employee);
            return $this->redirectToRoute('employees_form_update', ['id' => $id]);
        }

        return $this->render('/Employees/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}