<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 */

namespace Optime\SimpleReport\Bundle\Controller;

use http\Client\Response;
use Optime\SimpleReport\Bundle\Entity\SimpleReport;
use Optime\SimpleReport\Bundle\Service\GenericReportGenerator;
use Optime\SimpleReport\Bundle\Service\ReportGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/simplereport", name="simplereport_")
 */
class SimpleReportController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(ReportGenerator $reportGenerator)
    {
        $reports = $reportGenerator->getActiveReports();

        return $this->render('@OptimeSimpleReport/views/index.html.twig', [
            'reports'=>$reports
        ]);
    }

    /**
     * @Route("/generator/{slug}", name="generator")
     */
    public function generator($slug, ReportGenerator $reportGenerator)
    {
        $response = $reportGenerator->generateReportFromSlug($slug);

        if(!$response) {
            throw $this->createNotFoundException('This report is not available');
        }else{
            return $response;
        }
    }

    /**
     * @Route("/from/service/{slug}", name="from_service")
     */
    public function fromService(SimpleReport $simpleReport, GenericReportGenerator $genericReportGenerator)
    {
        if(!$simpleReport->getActive()) {
            throw $this->createNotFoundException('This report is not available');
        }
        return $genericReportGenerator->generate($simpleReport);
    }
}
