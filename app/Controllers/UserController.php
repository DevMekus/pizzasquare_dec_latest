<?php
namespace App\Controllers;

use App\Services\UserService;
use App\Utils\Response;
use App\Utils\RequestValidator;
use App\Utils\Utility;

class UserController{

    public function register(){
        try {
            $data = RequestValidator::validate([], $_POST);

            $data = RequestValidator::sanitize($data);
            $exist = UserService::fetchUserById($data['email_address']);
            if (!empty($exist)) Response::error(409, "user already exists");

            $created = UserService::createUser($data);

            if ($created['success']) {
                Response::success($created, "User registered successfully");
            } else {
                Response::error(500, "Failed to register user");
            }

        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'UserController::register', [], $th);
            Response::error(500, "Error updating creating profile");
        }
    }

    public function login(){
        try {
            $data = RequestValidator::validate([
                'email_address' => 'required|email',
                'user_password' => 'required|min:6',
            ]);
            
            $data = RequestValidator::sanitize($data);

            $loggedIn = UserService::authenticate($data['email_address'], $data['user_password']);

            if ($loggedIn) {
                Response::success($loggedIn, "Login successful");
            }

            Response::error(401, "Invalid login credentials");
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'UserController::login', [], $th);
            Response::error(500, "Error during login");
        }
    }

    public function getProfile($user_id){
        try {
            $id = RequestValidator::parseId($user_id);
             $user = UserService::fetchUserById($id);   

            if (empty($user)) {
                    Response::error(404, "User not found");
                    return;
            }

            Response::success($user, "User information");
        } catch (\Throwable $th) {
             Utility::log($th->getMessage(), 'error', 'UserController::getProfileById', [], $th);
            Response::error(500, "Error fetching user profile");
        }
    }

    public function getprofiles(){
        try {
             $users = UserService::fetchAllUsers();   

            if (empty($users)) {
                    Response::error(404, "No users found");
                    return;
            }

            Response::success($users, "Users information");
        } catch (\Throwable $th) {
             Utility::log($th->getMessage(), 'error', 'UserController::getprofiles', [], $th);
            Response::error(500, "Error fetching users");
        }
    }
  
    public function updateProfile($id){
        try {
            $id = RequestValidator::parseId($id);
            $data = RequestValidator::validate([
                'userid'      => 'required|min:3',
            ], $_POST);

            $data = RequestValidator::sanitize($data);
            $user = UserService::fetchUserById($id);

            if (empty($user)) {
                Response::error(404, "User not found");
                return;
            }


            if (UserService::updateUser($id, $data, $user[0]))
                Response::success([], "profile updated");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'UserController::updateProfile', [], $e);
            Response::error(500, "Error updating users profile");
        }
    }

    public function changePassword($userid){
        // Logic to change user password
    }

    public function logout(){
        try {
             $data = RequestValidator::validate([
                'userid' => 'required|min:7',
            ]);

            $data = RequestValidator::sanitize($data);
            $loggedOut = UserService::logout($data);

            if ($loggedOut) {
                Response::success([], "User logged out");
                return;
            }

            Response::error(500, "Logout failed");
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'UserController::logout', [], $th);
            Response::error(500, "Error during logout");
        }
    }

    public function recoverAccount(){
       try {
            $data = RequestValidator::validate([
                'email_address' => 'required|email',
            ]);

            $data = RequestValidator::sanitize($data);            
            $initiateReset = UserService::initiatePasswordReset($data);
            if ($initiateReset) {
                Response::success([], "A reset link has been sent to your registered email.");
                
            }
            Response::error(500, "Account recovery failed");
       } catch (\Throwable $e) {
          Response::error(500, "Recovery error: " . $e->getMessage());
       }
    }

    public function resetPassword(){
       try {
            $data = RequestValidator::validate([
                'token'        => 'required|min:10',
                'new_password' => 'required|min:6',
            ]);

            $data = RequestValidator::sanitize($data);
            $reset = UserService::resetPassword($data);
            if ($reset) {
                Response::success([], "Password has been reset successfully.");
                return;
            }
            Response::error(500, "Password reset failed");
       } catch (\Throwable $th) {
        //throw $th;
       }
    }

     public function deleteProfile($id)
    {
        try {
            $id = RequestValidator::parseId($id);
            $user = UserService::fetchUserById($id);
            if (empty($user)) {
                Response::error(404, "User not found");
                return;
            }

            if (UserService::deleteUserAccount($id,  $user[0]))
                Response::success([], "profile deleted");
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'AccountController::deleteProfile', [], $e);
            Response::error(500, "Error deleting users profile");
        }
    }
}