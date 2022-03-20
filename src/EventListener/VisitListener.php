<?php

namespace App\EventListener;

use App\Entity\Visit;
use App\Service\RemoteServerIpService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\SerializerInterface;

class VisitListener implements EventSubscriberInterface
{

    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer, private RemoteServerIpService $serverIpService)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            // don't do anything if it's not the main request
            return;
        }


        $request = $event->getRequest();

        $log = (new Visit())
            ->setClient($request->getClientIp())
            ->setRoute($request->getRequestUri())
            ->setServer($this->serverIpService->get())
            ->setData($request->request->all())
        ;

        $this->manager->persist($log);
        $this->manager->flush();

        $paths = [
            Path::normalize(sys_get_temp_dir() . '/local-logs/logs.csv'),
            Path::normalize('/srv/app/shared-logs/logs.csv'),
        ];

        try {
            $this->writeToLogFiles($log, $paths);
        } catch (\JsonException $e) {
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest'
        ];
    }

    /**
     * @throws \JsonException
     */
    private function writeToLogFiles(Visit $visit, array $paths): void
    {
        $filesystem = new Filesystem();

        array_walk($paths, function ($path) use ($filesystem, $visit) {
            if ($filesystem->exists($path)) {
                $context = ['no_headers' => true];
            }
            $content = $this->serializer->serialize($visit, 'csv', $context ?? []);
            $filesystem->appendToFile($path, $content);
        });
    }
}
