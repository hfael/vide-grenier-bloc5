<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Hash;
use \Core\View;
use Exception;

/**
 * User controller
 */
class User extends \Core\Controller
{
    private const REMEMBER_ME_SECONDS = 2592000; // 30 jours

    /**
     * Affiche la page de login
     */
    public function loginAction()
    {
        if (isset($_POST['submit'])) {
            $loginSuccess = $this->login($_POST);

            if ($loginSuccess) {
                header('Location: /account');
                exit;
            }

            View::renderTemplate('User/login.html', [
                'error' => 'Identifiants invalides.'
            ]);
            return;
        }

        View::renderTemplate('User/login.html');
    }

    /**
     * Page de creation de compte
     */
    public function registerAction()
    {
        if (isset($_POST['submit'])) {
            $data = $_POST;

            if (($data['password'] ?? '') !== ($data['password-check'] ?? '')) {
                View::renderTemplate('User/register.html', [
                    'error' => 'Les mots de passe ne correspondent pas.'
                ]);
                return;
            }

            $userID = $this->register($data);

            if ($userID && $this->login($data)) {
                header('Location: /account');
                exit;
            }

            View::renderTemplate('User/register.html', [
                'error' => 'Impossible de creer le compte avec ces informations.'
            ]);
            return;
        }

        View::renderTemplate('User/register.html');
    }

    /**
     * Affiche la page du compte
     */
    public function accountAction()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }

        $articles = Articles::getByUser($_SESSION['user']['id']);

        View::renderTemplate('User/account.html', [
            'articles' => $articles
        ]);
    }

    private function register($data)
    {
        try {
            $salt = Hash::generateSalt(32);

            return \App\Models\User::createUser([
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => Hash::generate($data['password'], $salt),
                'salt' => $salt
            ]);
        } catch (Exception $ex) {
            return null;
        }
    }

    private function login($data)
    {
        try {
            if (!isset($data['email'], $data['password'])) {
                return false;
            }

            $user = \App\Models\User::getByLogin($data['email']);

            if (!$user) {
                return false;
            }

            if (Hash::generate($data['password'], $user['salt']) !== $user['password']) {
                return false;
            }

            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];

            if (isset($data['remember_me'])) {
                $this->rememberCurrentSession();
            }

            return true;
        } catch (Exception $ex) {
            return false;
        }
    }

    private function rememberCurrentSession()
    {
        if (!session_id()) {
            return;
        }

        $params = session_get_cookie_params();
        setcookie(session_name(), session_id(), [
            'expires' => time() + self::REMEMBER_ME_SECONDS,
            'path' => $params['path'] ?: '/',
            'domain' => $params['domain'],
            'secure' => (bool) $params['secure'],
            'httponly' => (bool) $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax'
        ]);
    }

    /**
     * Logout: Delete cookie and session.
     */
    public function logoutAction()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();

        header('Location: /');
        return true;
    }
}
