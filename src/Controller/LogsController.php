<?php

namespace App\Controller;

use App\Entity\Visit;
use App\Repository\VisitRepository;
use App\Service\RemoteServerIpService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class LogsController extends AbstractController
{


    private VisitRepository $visitRepository;
    private string $localLogsFilePath;
    private string $pvcLogsFilePath;

    public function __construct(private EntityManagerInterface $manager, private RemoteServerIpService $serverIpService)
    {
        $this->visitRepository = $this->manager->getRepository(Visit::class);
        $this->localLogsFilePath = Path::normalize(sys_get_temp_dir().'/local-logs/logs.csv');
        $this->pvcLogsFilePath = Path::normalize('/srv/app/shared-logs/logs.csv');
    }

    #[Route('/', name: 'logs')]
    public function index(Request $request): Response
    {
        $serverData = [
            'clientName' => $request->server->get('SERVER_NAME'),
            'clientIp' => $request->getClientIp(),
            'localIp' => $this->serverIpService->get()
        ];

        $dbLogs = $this->visitRepository->findAll();

        $localFileContent = file_get_contents($this->localLogsFilePath);
        $pvcFileContent = file_get_contents($this->pvcLogsFilePath);

        return $this->render('base.html.twig', [
            'dbLogs' => $dbLogs,
            'localLogs' => $localFileContent,
            'sharedLogs' => $pvcFileContent,
            'serverData' => $serverData,
        ]);
    }

    #[Route('/clear', name: 'clear', methods: ['POST'])]
    public function clear(Request $request): Response
    {
        $type = $request->request->get('type');

        $filesystem = new Filesystem();

        if($type === 'database' || $type === 'all') {
            $this->visitRepository->deleteAll();
            $this->manager->flush();
        }

        if($type === 'local' || $type === 'all') {
            $filesystem->remove($this->localLogsFilePath);
        }

        if($type === 'shared' || $type === 'all') {
            $filesystem->remove($this->pvcLogsFilePath);
        }

        $responseData['database'] = $this->renderDatabaseEntriesTemplate();
        $responseData['local'] = $this->renderLogFileTemplate($this->localLogsFilePath);
        $responseData['shared'] = $this->renderLogFileTemplate($this->pvcLogsFilePath);

        return new JsonResponse($responseData);
    }

    public function renderLogFileTemplate(string $path): string {
        if(file_exists($path) &&  $fileContent =  file_get_contents($path)){
            return $this->renderView('components/log-csv-render.html.twig', ['logs' => $fileContent]);
        }
        return '';
    }
    public function renderDatabaseEntriesTemplate(): string {
        $dbLogs = $this->visitRepository->findAll();
        return $this->renderView('components/render-entities.html.twig', ['logs' => $dbLogs]);
    }
}
