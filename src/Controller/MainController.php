<?php
namespace App\Controller;

use App\Repository\EmployeeRepository;
use App\Repository\ProductionTimeRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\String\s;

final class MainController extends AbstractController
{
    public function  __construct(
        private ProjectRepository $projectRepository,
        private EmployeeRepository $employeeRepository,
        private ProductionTimeRepository $productionTimeRepository,
    ){
    }

    #[Route('/', name: 'main_homepage', methods: ['GET'])]
    public function homepage(): Response
    {
        //projects
        $projects = $this->projectRepository->findAllWithProductionTimes();

        $lastProjects = $this->projectRepository->findLastProjects(5);
        $lastProjectsTotalCost = [];
        foreach ($lastProjects as $lastProject){
            $totalCost = 0;
            foreach ($lastProject->getProductionTimes() as $productionTime){
                $totalCost += $productionTime->getEmployee()->getDailyCost() * $productionTime->getNbDays();
            }
            $lastProjectsTotalCost[] = $totalCost;
        }

        $deliveredProjects = 0;
        $inProgressProjects = 0;
        $profitableProjects = 0;
        foreach ($projects as $project){
            //delivered projects
            if($project->getDeliveryDate() == null){
                $inProgressProjects++;
            }else{
                $deliveredProjects++;
            }

            //profitable projects
            $totalCost = 0;
            foreach ($project->getProductionTimes() as $productionTime){
                $totalCost += $productionTime->getEmployee()->getDailyCost() * $productionTime->getNbDays();
            }
            if($totalCost < $project->getPrice())
                $profitableProjects++;

        }
        $profitableRate = round($profitableProjects / sizeof($projects) * 100);
        $deliveryRate = round($deliveredProjects / sizeof($projects) * 100);


        //employees
        $employees = $this->employeeRepository->findAllWithProductionTimes();
        $nbEmployees = sizeof($employees);
        $bestEmployee = null;
        $bestTotalCost = 0;

        foreach ($employees as $employee){
            if($bestEmployee === null)
                $bestEmployee = $employee;
            else{
                $totalCost = 0;
                foreach ($employee->getProductionTimes() as $productionTime){
                    $totalCost += $productionTime->getNbDays() * $productionTime->getEmployee()->getDailyCost();
                }
                if($totalCost > $bestTotalCost){
                    $bestEmployee = $employee;
                    $bestTotalCost = $totalCost;
                }
            }
        }


        //production times
        $productionTimes = $this->productionTimeRepository->findAll();
        $lastProductionTimes = $this->productionTimeRepository->findLastProductionTimes(10);

        $productionDays = 0;
        foreach ($productionTimes as $productionTime){
            $productionDays += $productionTime->getNbDays();
        }


        return $this->render('index.html.twig', [
            'deliveredProjects' => $deliveredProjects,
            'inProgressProjects' => $inProgressProjects,
            'nbEmployees' => $nbEmployees,
            'productionDays' => $productionDays,
            'deliveryRate' => $deliveryRate,
            'profitableRate' => $profitableRate,
            'lastProjects' => $lastProjects,
            'lastProjectsTotalCost' => $lastProjectsTotalCost,
            'bestEmployee' => $bestEmployee,
            'bestTotalCost' => $bestTotalCost,
            'lastProductionTimes' => $lastProductionTimes,
        ]);
    }

}