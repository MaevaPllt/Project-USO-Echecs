<?php

/**
 * Created by PhpStorm.
 * User: aurelwcs
 * Date: 08/04/19
 * Time: 18:40
 */

namespace App\Controller;

use App\Model\CompetitionManager;

class AdminCompetitionController extends AbstractController
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
        $competition = $competitionManager->selectAll();
        return $this->twig->render('Admin/adminCompetition.html.twig', [
            'competitions' => $competition,
        ]);
    }

    public function add()
    {
        $errors = [];
        $competitionManager = new CompetitionManager();
        $competition = $competitionManager->selectAll();

        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $item = array_map('trim', $_POST);
            $errors = $this->validate($item);
            if (empty($errors)) {
                $uploadDir = 'uploads/';
                $extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $uploadFile = $uploadDir . basename($filename);
                move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile);
                $item['picture'] = $filename;
                $adminCompetition = new CompetitionManager();
                $adminCompetition->add($item);
                header('Location: /AdminCompetition/index');
            }
        }
        return $this->twig->render('Admin/adminCompetition.html.twig', [
            'errors' => $errors,
            'competitions' => $competition
            ]);
    }

    public function validate(array $item)
    {
        $errors = [];
        $mime = ['image/jpeg', 'image/png', 'image/gif'];

        if (empty($item['name'])) {
            $errors[] = 'Le champ concernant le titre ne doit pas être vide';
        }
        if (empty($item['description'])) {
            $errors[] = 'Le champ concernant le contenu ne doit pas être vide';
        }
        if (empty($item['date'])) {
            $errors[] = 'Le champ concernant la date ne doit pas être vide';
        }
        if (empty($item['address'])) {
            $errors[] = "Le champ concernant l'adresse ne doit pas être vide";
        }
        if (empty($_FILES['picture']['name'])) {
            $errors[] = "Erreur! Aucune image séléctionnée.";
        }
        if ($_FILES['picture']['size'] > 1000000) {
            $errors[] = "Erreur! Image trop volumineuse.";
        }

        if (!in_array($_FILES['picture']['type'], $mime)) {
            $errors[] = "Erreur! Type d'image invalide.";
        }
        return $errors;
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $id = $_POST['id'];
            $competitionManager = new CompetitionManager();
            $competitionManager->delete($id);
            header('Location: /AdminCompetition/index');
        }
    }

    public function update(int $id)
    {
        $errors = [];
        $competitionManager = new CompetitionManager();
        $item = $competitionManager->selectOneById($id);
        $initialPicture = $item['picture'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $item = array_map('trim', $_POST);
            $errors = $this->validateUpdate($item, $_FILES['picture']);

            if (empty($errors)) {
                $uploadDir = 'uploads/';
                if (!empty($_FILES['picture']['tmp_name'])) {
                    $extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $extension;
                    $uploadFile = $uploadDir . basename($filename);
                    move_uploaded_file($_FILES['picture']['tmp_name'], $uploadFile);
                    $item['picture'] = $filename;
                    unlink('uploads/' . $initialPicture);
                } else {
                    $item['picture'] = $initialPicture;
                }
                $competitionManager = new CompetitionManager();
                $competitionManager->update($item);
            }
        }
        $adminCompetition = new CompetitionManager();
        $competition = $adminCompetition->selectAll();
        return $this->twig->render('Admin/adminCompetition.html.twig', [
            'errors' => $errors,
            'competitions' => $competition,
            'item' => $item,
        ]);
    }

    public function validateUpdate(array $data, array $files)
    {
        $errors = [];
        $fileSize = 1000000;
        $mime = ['image/jpeg', 'image/png', 'image/gif'];

        if (empty($data['name'])) {
            $errors[] = 'Le champ concernant le titre ne doit pas être vide';
        }
        if (empty($data['description'])) {
            $errors[] = 'Le champ concernant le contenu ne doit pas être vide';
        }
        if (empty($data['date'])) {
            $errors[] = 'Le champ concernant la date ne doit pas être vide';
        }
        if (empty($data['address'])) {
            $errors[] = "Le champ concernant l'adresse ne doit pas être vide";
        }
        if ($files['size'] > $fileSize) {
            $errors[] = 'Le fichier ne doit pas excéder ' . $fileSize / 1000000 . ' Mo';
        }
        if (!empty($files['tmp_name']) && !in_array(mime_content_type($files['tmp_name']), $mime)) {
            $errors[] = 'Ce type de fichier n\'est pas valide';
        }
        return $errors;
    }
}
