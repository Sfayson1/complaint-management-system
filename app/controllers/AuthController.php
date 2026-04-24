<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Customer.php';

class AuthController {
    private $userModel;
    private $customerModel;

    public function __construct($db) {
        $this->userModel = new User($db);
        $this->customerModel = new Customer($db);
    }

    public function login($email, $password) {
        $user = $this->userModel->findByEmail($email);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['first_name'] = $user['first_name'];

            return $user;
        }

        return false;
    }

    public function register($email, $password, $firstName, $lastName, $streetAddress, $city, $state, $zipCode, $phone) {
        $userId = $this->userModel->create($email, $password);

        if (!$userId) {
            return false;
        }

        return $this->customerModel->create($userId, $firstName, $lastName, $streetAddress, $city, $state, $zipCode, $phone);
    }
}
