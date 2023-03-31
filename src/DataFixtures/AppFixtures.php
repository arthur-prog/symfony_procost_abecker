<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\Job;
use App\Entity\ProductionTime;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class AppFixtures extends Fixture
{
    private $manager;

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadJobs();

        $this->loadEmployees();

        $this->loadProjects();

        $this->loadProductionTimes();

        $manager->flush();
    }

    /**
     * @throws Exception
     */
    private function loadJobs(): void
    {
        $jobs = [
            ['Front developer'],
            ['Back developer'],
            ['Fullstack developer'],
            ['Designer'],
            ['Project manager'],
            ['Digital marketing'],
            ['SEO'],
            ['Content writer'],
            ['Customer service'],
        ];

        foreach ($jobs as $key => [$name]) {
            $job = new Job();
            $job->setName($name);
            $this->manager->persist($job);
            $this->addReference(Job::class . $key, $job);
        }
    }

    /**
     * @throws Exception
     */
    private function loadEmployees(): void
    {
        $firstNames = [
            "John",
            "Jane",
            "Bob",
            "Alice",
            "Jack",
            "Jill",
            "James",
            "Mary",
        ];

        $lastNames = [
            "Doe",
            "Smith",
            "Johnson",
            "Williams",
            "Brown",
            "Jones",
            "Miller",
            "Davis",
        ];

        for($i = 0; $i < 50; $i++) {
            $employee = new Employee();
            $employee->setFirstName($firstNames[array_rand($firstNames)]);
            $employee->setLastName($lastNames[array_rand($lastNames)]);
            $employee->setMail(lcfirst($employee->getFirstName()) . '.' . lcfirst($employee->getLastName()) . '@procost.com');

            /** @var Job $job */
            $job = $this->getReference(Job::class . random_int(0, 8));
            $employee->setJob($job);

            $employee->setDailyCost(random_int(200, 500));
            $employee->setHiringDate(new \DateTimeImmutable('now - ' . random_int(0, 365) . ' days'));
            $this->manager->persist($employee);
            $this->addReference(Employee::class . $i, $employee);
        }
    }

    /**
     * @throws Exception
     */
    private function loadProjects(): void
    {
        for($i = 0; $i < 30; $i++) {
            $project = new Project();
            $project->setName('Project ' . $i);
            $project->setDescription('Description of project ' . $i);
            $project->setPrice(random_int(50000, 100000));
            if(random_int(0, 1) === 1)
                $project->setDeliveryDate(new \DateTimeImmutable('now - ' . random_int(0, 365) . ' days'));
            $project->setCreatedAt(new \DateTimeImmutable('now - ' . random_int(0, 365) . ' days'));
            $this->manager->persist($project);
            $this->addReference(Project::class . $i, $project);
        }
    }

    /**
     * @throws Exception
     */
    private function loadProductionTimes(): void
    {
        for($i = 0; $i < 1000; $i++) {
            $productionTime = new ProductionTime();

            /** @var Employee $employee */
            $employee = $this->getReference(Employee::class . random_int(0, 49));
            $productionTime->setEmployee($employee);

            /** @var Project $project */
            $project = $this->getReference(Project::class . random_int(0, 29));
            $productionTime->setProject($project);

            $productionTime->setNbDays(random_int(1, 10));
            $this->manager->persist($productionTime);
        }
    }
}
