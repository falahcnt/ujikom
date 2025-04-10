<?php
require_once '../models/User.php';
session_start();

class AuthController {
    private $user;

    public function __construct($db) {
        $this->user = new User($db);
    }

    public function login() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->user->login($username, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                if ($user['role'] == 'admin') {
                    header("Location: /views/dashboard.php");
                } else {
                    header("Location: ../../home.php");
                }
            } else {
                echo "Login gagal!";
            }
        }
    }
    

    public function logout() {
        session_destroy();
        header("Location: /login.php");
    }
}
?>
