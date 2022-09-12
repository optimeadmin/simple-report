<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 */

namespace Optime\SimpleReport\Bundle\Controller;

use Optime\SimpleReport\Entity\SimpleReport;
use Optime\SimpleReport\Service\GenericReportGenerator;
use Optime\SimpleReport\Service\QuestionUserEventService;
use Optime\SimpleReport\Service\ReportGenerator;
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

        return $this->render('reports/index.html.twig', [
            'reports'=>$reports
        ]);
    }

    /**
     * @Route("/generator/{slug}", name="generator")
     */
    public function generator($slug, ReportGenerator $reportGenerator)
    {
        return $reportGenerator->generateReportFromSlug($slug);
    }

    /**
     * @Route("/from/service/{slug}", name="from_service")
     */
    public function fromService(SimpleReport $simpleReport, GenericReportGenerator $genericReportGenerator)
    {
        return $genericReportGenerator->generate($simpleReport);
    }
}
