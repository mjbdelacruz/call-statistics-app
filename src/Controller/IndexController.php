<?php

namespace App\Controller;

use App\Form\CSVFileType;
use App\Service\CallLogService;
use App\Service\FileUploaderService;
use App\Service\StatisticsService;
use App\Utils\CSVParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class IndexController extends AbstractController
{
    private Environment $twig;

    private FileUploaderService $fileUploader;

    private CallLogService $callLogService;

    private StatisticsService $statisticsService;

    public function __construct(Environment $twig, FileUploaderService $fileUploader, CallLogService $callLogService, StatisticsService $statisticsService)
    {
        $this->twig = $twig;
        $this->fileUploader = $fileUploader;
        $this->callLogService = $callLogService;
        $this->statisticsService = $statisticsService;
    }


    /**
     * @Route("/statistics", name="index", methods={"GET|POST"})
    **/
    public function statistics(Request $request): Response
    {
        $form = $this->createForm(CSVFileType::class);
        $form->handleRequest($request);

        if ($request->getMethod() === "POST" && $form->isSubmitted()) {
            if ($form->isValid()) {
                /** @var UploadedFile $csvFile */
                $csvFile = $form->get('calls_log')->getData();

                // Parse Uploaded File
                $callLogs = $this->callLogService->parse($csvFile);
                if (!empty($callLogs)) {
                    // Store Parsed Uploaded File
                    $this->callLogService->save($callLogs);

                    // Upload File
                    $fileUpload = $this->fileUploader->upload($csvFile);

                    // Store FileUpload details to database
                    $this->fileUploader->save($fileUpload);

                    $this->addFlash('upload_success', 'File uploaded successfully.');
                } else {
                    $this->addFlash('upload_failure', "CSV file is invalid.");
                }

                return $this->redirect($request->getUri());
            } else {
                $errors = $form->getErrors(true);
                $errorMessages = "";
                foreach ($errors AS $error) {
                    $errorMessages .= sprintf("%s.", $error->getMessage());
                }
                $this->addFlash('upload_failure', $errorMessages);
            }
        }

        $pollingInfo = $this->callLogService->getPollingInfo();
        $statistics = $this->statisticsService->getStatistics();

        return $this->renderForm('index.html.twig', [
            'form' => $form,
            'statistics' => $statistics,
            'pollingInfo' => $pollingInfo
        ]);
    }

    /**
     * @Route("/statistics/poll", name="poll", methods={"GET"})
     **/
    public function poll(Request $request)
    {
        $previousPollingCount = $request->get('pollingCount');
        $previousPollingMaxDateTime = $request->get('pollingMaxDateTime');

        $currentPollingInfo = $this->callLogService->getPollingInfo();
        if ($currentPollingInfo['totalRecord'] != $previousPollingCount || $currentPollingInfo['maxDateTime'] > $previousPollingMaxDateTime) {
            return new JsonResponse(['dataUpdated' => true]);
        }

        return new JsonResponse(['dataUpdated' => false]);
    }
}