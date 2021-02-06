<?php

namespace App\Controller;

use App\Model\InscriptionManager;
use App\Model\PartnerManager;

/**
 * Class AdminController
 * @package App\Controller
 *
 * @SuppressWarnings(PHPMD)
 */

class AdminController extends AbstractController
{
    /**
     * Display home admin page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     */
    public function home()
    {
        return $this->twig->render('Admin/home.html.twig');
    }

    /**
     * Display members admin page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     */
    public function members()
    {
        $inscriptionManager = new InscriptionManager();
        $members = $inscriptionManager->selectValidate();

        return $this->twig->render('Admin/Members/adminMembers.html.twig', [
            'members' => $members,
        ]);
    }

    public function addMemberAdmin()
    {
        $inscriptionManager = new InscriptionManager();
        $members = $inscriptionManager->selectAll();
        $data = [];
        $errorsAdd = [];
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $data = array_map('trim', $_POST);
            $errorsAdd = $this->memberValidation($data);
            if (empty($errorsAdd)) {
                if (empty($data['status'])) {
                    $data['status'] = null;
                }
                $inscriptionManager = new InscriptionManager();
                $inscriptionManager->addMember($data);

                header('Location: /admin/members');
            }
        }

        return $this->twig->render('Admin/Members/adminMembers.html.twig', [
            'errorsAdd' => $errorsAdd,
            'data' => $data,
            'members' => $members
        ]);
    }

    public function editMember(int $id)
    {
        $inscriptionManager = new InscriptionManager();
        $memberEdit = $inscriptionManager->selectOneById($id);
        $errorsEdit = [];
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $memberEdit = array_map('trim', $_POST);
            $errorsEdit = $this->memberValidation($memberEdit);
            if (empty($errorsEdit)) {
                if (empty($memberEdit['status'])) {
                    $memberEdit['status'] = null;
                }
                $inscriptionManager->updateMember($memberEdit);
                header('Location: /admin/members');
            }
        }
        $members = $inscriptionManager->selectAll();
        return $this->twig->render('Admin/Members/adminMembers.html.twig', [
            'errorsEdit' => $errorsEdit,
            'members' => $members,
            'memberEdit' => $memberEdit
        ]);
    }

    public function deleteMember()
    {
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $id = $_POST['id'];
            $inscriptionManager = new InscriptionManager();
            $inscriptionManager->delete($id);
            header('Location: /admin/members');
        }
    }

    public function refuseMember()
    {
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $id = $_POST['id'];
            $inscriptionManager = new InscriptionManager();
            $inscriptionManager->delete($id);
            header('Location: /admin/acceptMembers');
        }
    }

    public function acceptMembers()
    {
        $inscriptionManager = new InscriptionManager();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $inscriptionManager->acceptMember($id);
            header("Location:/admin/acceptMembers");
        }
        $members = $inscriptionManager->selectNonValidate();
        return $this->twig->render('Admin/Members/validateMembers.html.twig', [
            'members' => $members
        ]);
    }

    /**
     *
     * @return array
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @SuppressWarnings(PHPMD)
     */
    private function memberValidation(array $data)
    {
        $errors = [];
        $maxlength = 100;

        if (empty($data['firstname'])) {
            $errors[] = 'Le prénom est requis';
        }
        if (strlen($data['firstname']) > $maxlength) {
            $errors[] = 'Le prénom ne doit pas avoir plus de ' . $maxlength . ' caractères.';
        }
        if (empty($data['lastname'])) {
            $errors[] = 'Le nom est requis';
        }
        if (strlen($data['lastname']) > $maxlength) {
            $errors[] = 'Le nom ne doit pas avoir plus de ' . $maxlength . ' caractères.';
        }
        if (empty($data['email'])) {
            $errors[] = 'L\'email est requis';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Le format de l\'email est invalide';
        }
        if (empty($data['phone'])) {
            $errors[] = 'Le numéro de téléphone est requis';
        }
        if (empty($data['birthday'])) {
            $errors[] = 'La date de naissance est requise';
        }
        if (empty($data['address'])) {
            $errors[] = 'L\'adresse est requise';
        }
        if (strlen($data['address']) > $maxlength) {
            $errors[] = 'L\'adresse ne doit pas avoir plus de ' . $maxlength . ' caractères.';
        }
        if (empty($data['postal_code'])) {
            $errors[] = 'Le code postal est requis';
        } elseif ((!is_numeric($data['postal_code'])) || (strlen($data['postal_code']) != 5)) {
            $errors[] = 'Votre Code postal n\'est pas correct';
        }
        if (empty($data['city'])) {
            $errors[] = 'La ville  est requise';
        } elseif (strlen($data['city']) > $maxlength) {
            $errors[] = 'La ville ne doit pas avoir plus de ' . $maxlength . ' caractères.';
        }
        return $errors;
    }

    /**
     * Display partners admin page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     */
    public function partners()
    {
        $partnersManager = new PartnerManager();
        $partners = $partnersManager->selectAll();

        return $this->twig->render('Admin/Partners/adminPartners.html.twig', [
            'partners' => $partners,
        ]);
    }

    public function addPartnerAdmin()
    {
        $partnerManager = new PartnerManager();
        $partners = $partnerManager->selectAll();
        $data = [];
        $errorsAdd = [];
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $data = array_map('trim', $_POST);
            $errorsAdd = $this->partnerValidation($data, $_FILES['image'], 'add');
            if (empty($errorsAdd)) {
                $filename = uniqid() . '.' . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $filename);
                $data['image'] = $filename;
                $partnerManager->addPartner($data);

                header('Location: /admin/partners');
            }
        }

        return $this->twig->render('Admin/Partners/adminPartners.html.twig', [
            'errorsAdd' => $errorsAdd,
            'data' => $data,
            'partners' => $partners
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @SuppressWarnings(PHPMD)
     */
    public function editPartner(int $id)
    {
        $partnerManager = new PartnerManager();
        $partnerEdit = $partnerManager->selectOneById($id);
        $initialPicture = $partnerEdit['image'];
        $errorsEdit = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $partnerEdit = array_map('trim', $_POST);

            $errorsEdit = $this->partnerValidation($partnerEdit, $_FILES['image'], 'update');
            if (empty($errorsEdit)) {
                $uploadDir = 'uploads/';
                if (!empty($_FILES['image']['tmp_name'])) {
                    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $extension;
                    $uploadFile = $uploadDir . basename($filename);
                    move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile);
                    $partnerEdit['image'] = $filename;
                    unlink('uploads/' . $initialPicture);
                } else {
                    $partnerEdit['image'] = $initialPicture;
                }
                $partnerManager->updatepartner($partnerEdit);
                header('Location: /admin/partners');
            }
        }

        $partners = $partnerManager->selectAll();
        return $this->twig->render('Admin/Partners/adminPartners.html.twig', [
            'errorsEdit' => $errorsEdit,
            'partners' => $partners,
            'partnerEdit' => $partnerEdit
        ]);
    }

    public function deletePartner()
    {
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $id = $_POST['id'];
            $partnerManager = new PartnerManager();
            $partnerManager->delete($id);
            header('Location: /admin/partners');
        }
    }

    private function partnerValidation(array $data, array $files, string $method)
    {
        $errors = [];
        $maxlength = 100;
        $fileSize = 1000000;
        $authorizedMimes = ['image/jpeg', 'image/png', 'image/gif'];

        if (empty($data['name'])) {
            $errors[] = 'Le nom du partenaire ne doit pas être vide';
        } elseif (strlen($data['name']) > $maxlength) {
            $errors[] = 'Le nom doit faire moins de ' . $maxlength . ' caractères';
        }
        if (!filter_var($data['url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Le format de l\'URL n\'est pas valide';
        }
        if ($method === 'add') {
            if (empty($files['tmp_name'])) {
                $errors[] = 'Le fichier ne peut pas être manquant';
            }
        }
        if ($files['size'] > $fileSize) {
            $errors[] = 'Le fichier ne doit pas excéder ' . $fileSize / 1000000 . ' Mo';
        }
        if (!empty($files['tmp_name']) && !in_array(mime_content_type($files['tmp_name']), $authorizedMimes)) {
            $errors[] = 'Ce type de fichier n\'est pas valide';
        }

        return $errors;
    }
}
