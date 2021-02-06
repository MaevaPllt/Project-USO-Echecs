<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\CompetitionManager;
use App\Model\RankingManager;

class CompetitionController extends AbstractController
{
    /**
     * Display home page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $competitionManager = new CompetitionManager();
        $competitions = $competitionManager->competitionDateArchive();
        $newCompetitions = $competitionManager->competitionNewDate();
        return $this->twig->render('Competition/competition.html.twig', [
            'competitions' => $competitions,
            'newCompetitions' => $newCompetitions,
        ]);
    }

    public function ranking($id)
    {
        $rankingManager = new RankingManager();
        $items = $rankingManager->ranking($id);
        return $this->twig->render('Competition/ranking.html.twig', [
            'items' => $items,
        ]);
    }
}
