<?php

namespace App\Controller;

use App\Model\NewsManager;

class NewsController extends AbstractController
{

    public const TITLE_LENGTH = 255;
    public const EXCERPT_LENGTH = 1000;
    public const CONTENT_LENGTH = 10000;

    /**
     * Display news admin page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     */

    public function index()
    {
        $newsManager = new NewsManager();
        $news = $newsManager->selectAll();

        return $this->twig->render('News/news.html.twig', ['news' => $news]);
    }
    public function show(int $id)
    {
        $newsManager = new NewsManager();
        $newsItem = $newsManager->selectOneById($id);

        return $this->twig->render('News/show.html.twig', ['newsItem' => $newsItem]);
    }

    public function admin()
    {
        $newsManager = new NewsManager();
        $news = $newsManager->selectAll();

        return $this->twig->render('Admin/adminNews.html.twig', [
            'news' => $news,
        ]);
    }

    public function add()
    {
        $newsManager = new NewsManager();
        $news = $newsManager->selectAll();
        $newsData = [];
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newsData = array_map('trim', $_POST);
            $errors = $this->newsValidate($newsData, $_FILES['cover_image']);
            if (empty($errors)) {
                $filename = uniqid() . '.' . pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES['cover_image']['tmp_name'], 'uploads/' . $filename);
                $newsData['cover_image'] = $filename;
                $newsManager = new NewsManager();
                $newsManager->addNews($newsData);

                header('Location: /news/admin');
            }
        }
        return $this->twig->render('Admin/adminNews.html.twig', [
            'errors' => $errors,
            'newsData' => $newsData,
            'news' => $news
        ]);
    }

    public function delete()
    {
        if ($_SERVER["REQUEST_METHOD"] === 'POST') {
            $id = $_POST['id'];
            $newsManager = new NewsManager();
            $newsManager->deleteNews($id);
            header('Location: /news/admin');
        }
    }

    public function edit(int $id)
    {
        $errors = [];
        $newsManager = new NewsManager();
        $items = $newsManager->selectOneById($id);
        $initialPicture = $items['cover_image'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $items = array_map('trim', $_POST);
            $errors = $this->newsEditValidate($items, $_FILES['cover_image']);

            if (empty($errors)) {
                $uploadDir = 'uploads/';
                if (!empty($_FILES['cover_image']['tmp_name'])) {
                    $extension = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
                    $filename = uniqid() . '.' . $extension;
                    $uploadFile = $uploadDir . basename($filename);
                    move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadFile);
                    $items['cover_image'] = $filename;
                    unlink('uploads/' . $initialPicture);
                } else {
                    $items['cover_image'] = $initialPicture;
                }
                $newsManager = new NewsManager();
                $newsManager->updateNews($items);
            }
        }
        $adminNews = new NewsManager();
        $news = $adminNews->selectAll();
        return $this->twig->render('Admin/adminNews.html.twig', [
            'errors' => $errors,
            'news' => $news,
            'items' => $items,
        ]);
    }

    /**
     * @param array $newsData
     * @param array $files
     * @return array
     *
     * @SuppressWarnings(PHPMD)
     */
    private function newsValidate(array $newsData, array $files): array
    {
        $errors = [];
        $fileSize = 1000000;
        $authorizedMimes = ['image/jpeg', 'image/png', 'image/gif'];

        if (empty($newsData['title'])) {
            $errors[] = 'Le titre ne doit pas être vide';
        }
        if (strlen($newsData['title']) > self::TITLE_LENGTH) {
            $errors[] = 'Le titre doit faire moins de ' . self::TITLE_LENGTH . ' caractères';
        }
        if (empty($newsData['content'])) {
            $errors[] = 'Le contenu de l\'article ne doit pas être vide';
        }
        if (strlen($newsData['content']) > self::CONTENT_LENGTH) {
            $errors[] = 'Le contenu doit faire moins de ' . self::CONTENT_LENGTH . ' caractères';
        }
        if (empty($newsData['excerpt'])) {
            $errors[] = 'Le contenu de l\'extrait ne doit pas être vide';
        }
        if (strlen($newsData['excerpt']) > self::EXCERPT_LENGTH) {
            $errors[] = 'L\'extrait doit faire moins de ' . self::EXCERPT_LENGTH . ' caractères';
        }
        if ($files['size'] > $fileSize) {
            $errors[] = 'Le fichier ne doit pas excéder ' . $fileSize / 1000000 . ' Mo';
        }
        if (empty($files['tmp_name'])) {
            $errors[] = 'Le fichier ne peux pas être manquant';
        }
        if (!empty($files['tmp_name']) && !in_array(mime_content_type($files['tmp_name']), $authorizedMimes)) {
            $errors[] = 'Ce type de fichier n\'est pas valide';
        }

        return $errors ?? [];
    }

    /**
     * @param array $newsData
     * @param array $files
     * @return array
     *
     * @SuppressWarnings(PHPMD)
     */
    private function newsEditValidate(array $newsData, array $files): array
    {
        $errors = [];
        $fileSize = 1000000;
        $authorizedMimes = ['image/jpeg', 'image/png', 'image/gif'];

        if (empty($newsData['title'])) {
            $errors[] = 'Le titre ne doit pas être vide';
        }
        if (strlen($newsData['title']) > self::TITLE_LENGTH) {
            $errors[] = 'Le titre doit faire moins de ' . self::TITLE_LENGTH . ' caractères';
        }
        if (empty($newsData['content'])) {
            $errors[] = 'Le contenu de l\'article ne doit pas être vide';
        }
        if (strlen($newsData['content']) > self::CONTENT_LENGTH) {
            $errors[] = 'Le contenu doit faire moins de ' . self::CONTENT_LENGTH . ' caractères';
        }
        if (empty($newsData['excerpt'])) {
            $errors[] = 'Le contenu de l\'extrait ne doit pas être vide';
        }
        if (strlen($newsData['excerpt']) > self::EXCERPT_LENGTH) {
            $errors[] = 'L\'extrait doit faire moins de ' . self::EXCERPT_LENGTH . ' caractères';
        }
        if ($files['size'] > $fileSize) {
            $errors[] = 'Le fichier ne doit pas excéder ' . $fileSize / 1000000 . ' Mo';
        }
        if (!empty($files['tmp_name']) && !in_array(mime_content_type($files['tmp_name']), $authorizedMimes)) {
            $errors[] = 'Ce type de fichier n\'est pas valide';
        }

        return $errors ?? [];
    }
}
