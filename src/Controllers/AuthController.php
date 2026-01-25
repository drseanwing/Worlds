<?php

namespace Worlds\Controllers;

use Worlds\Config\View;
use Worlds\Config\Response;
use Worlds\Config\Request;
use Worlds\Config\Database;

/**
 * AuthController class
 *
 * Handles user authentication including login, registration, and logout.
 */
class AuthController
{
    /**
     * Display the login form
     *
     * @param Request $request HTTP request object
     * @return Response HTTP response with login form
     */
    public function showLoginForm(Request $request): Response
    {
        $view = new View();
        $html = $view->render('auth/login', [
            'errors' => get_flash('errors') ?? [],
            'old' => $_SESSION['_old_input'] ?? []
        ]);

        // Clear old input after displaying
        clear_old_input();

        return Response::html($html);
    }

    /**
     * Process login POST request
     *
     * Validates credentials and creates user session.
     *
     * @param Request $request HTTP request object
     * @return Response Redirect response
     */
    public function login(Request $request): Response
    {
        // Validate CSRF token
        $token = $request->input('_csrf_token');
        if (!verify_csrf_token($token)) {
            flash('errors', ['CSRF token validation failed']);
            flash_old_input($request->getPost());
            return redirect('/login');
        }

        $username = $request->input('username', '');
        $password = $request->input('password', '');

        // Validation
        $errors = [];

        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        }

        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            flash_old_input($request->getPost());
            return redirect('/login');
        }

        // Attempt login
        $db = Database::getInstance();
        $stmt = $db->prepare('SELECT id, username, password_hash FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('errors', ['Invalid username or password']);
            flash_old_input(['username' => $username]);
            return redirect('/login');
        }

        // Start session and store user data
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        flash('success', 'Login successful');

        return redirect('/');
    }

    /**
     * Display the registration form
     *
     * @param Request $request HTTP request object
     * @return Response HTTP response with registration form
     */
    public function showRegisterForm(Request $request): Response
    {
        $view = new View();
        $html = $view->render('auth/register', [
            'errors' => get_flash('errors') ?? [],
            'old' => $_SESSION['_old_input'] ?? []
        ]);

        // Clear old input after displaying
        clear_old_input();

        return Response::html($html);
    }

    /**
     * Process registration POST request
     *
     * Validates input and creates new user account.
     *
     * @param Request $request HTTP request object
     * @return Response Redirect response
     */
    public function register(Request $request): Response
    {
        // Validate CSRF token
        $token = $request->input('_csrf_token');
        if (!verify_csrf_token($token)) {
            flash('errors', ['CSRF token validation failed']);
            flash_old_input($request->getPost());
            return redirect('/register');
        }

        $username = $request->input('username', '');
        $password = $request->input('password', '');
        $passwordConfirm = $request->input('password_confirm', '');

        // Validation
        $errors = [];

        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'Username must be at least 3 characters';
        }

        if (empty($password) || strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Password confirmation does not match';
        }

        // Check for duplicate username
        if (empty($errors)) {
            $db = Database::getInstance();
            $stmt = $db->prepare('SELECT id FROM users WHERE username = :username');
            $stmt->execute(['username' => $username]);

            if ($stmt->fetch()) {
                $errors[] = 'Username already exists';
            }
        }

        if (!empty($errors)) {
            flash('errors', $errors);
            flash_old_input(['username' => $username]);
            return redirect('/register');
        }

        // Create user
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $db = Database::getInstance();
        $stmt = $db->prepare(
            'INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)'
        );

        $stmt->execute([
            'username' => $username,
            'password_hash' => $passwordHash
        ]);

        // Auto-login after registration
        $userId = $db->lastInsertId();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;

        flash('success', 'Registration successful! Welcome to Worlds.');

        return redirect('/');
    }

    /**
     * Process logout request
     *
     * Destroys user session and redirects to login.
     *
     * @param Request $request HTTP request object
     * @return Response Redirect response
     */
    public function logout(Request $request): Response
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destroy all session data
        $_SESSION = [];

        // Destroy the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Destroy the session
        session_destroy();

        flash('success', 'You have been logged out');

        return redirect('/login');
    }
}
