<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Upload;
use \Core\View;

/**
 * Product controller
 */
class Product extends \Core\Controller
{
    /**
     * Affiche la page d'ajout
     */
    public function indexAction()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        if (isset($_POST['submit'])) {
            try {
                $data = $_POST;
                $data['user_id'] = $_SESSION['user']['id'];

                if (empty(trim($data['name'] ?? '')) || empty(trim($data['description'] ?? ''))) {
                    View::renderTemplate('Product/Add.html', [
                        'error' => 'Le titre et la description sont obligatoires.'
                    ]);
                    return;
                }

                $id = Articles::save($data);

                if ($this->hasUploadedPicture()) {
                    $pictureName = Upload::uploadFile($_FILES['picture'], $id);
                    Articles::attachPicture($id, $pictureName);
                }

                header('Location: /product/' . $id);
                exit;
            } catch (\Exception $e) {
                View::renderTemplate('Product/Add.html', [
                    'error' => $e->getMessage()
                ]);
                return;
            }
        }

        View::renderTemplate('Product/Add.html');
    }

    /**
     * Affiche la page d'un produit
     */
    public function showAction()
    {
        $id = $this->route_params['id'];
        $contactSent = false;

        try {
            if (isset($_POST['contact_submit'])) {
                $contactSent = $this->isValidContactRequest($_POST);
            }

            Articles::addOneView($id);
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);

            if (!$article) {
                throw new \Exception('Annonce introuvable.');
            }
        } catch (\Exception $e) {
            View::renderTemplate('404.html');
            return;
        }

        View::renderTemplate('Product/Show.html', [
            'article' => $article[0],
            'suggestions' => $suggestions,
            'contactSent' => $contactSent
        ]);
    }

    private function hasUploadedPicture()
    {
        return isset($_FILES['picture'])
            && $_FILES['picture']['error'] !== UPLOAD_ERR_NO_FILE
            && !empty($_FILES['picture']['name']);
    }

    private function isValidContactRequest($data)
    {
        return !empty(trim($data['contact_name'] ?? ''))
            && filter_var($data['contact_email'] ?? '', FILTER_VALIDATE_EMAIL)
            && !empty(trim($data['contact_message'] ?? ''));
    }
}
