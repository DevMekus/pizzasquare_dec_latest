<?php
namespace App\Services;

use App\Middleware\AuthMiddleware;
use App\Utils\Response;
use App\Utils\Utility;
use configs\Database;

class UserService{

    public static function createUser($data){
        $profile = Utility::$profile_tbl;
        $accounts = Utility::$accounts;
        try {
            $existingUser = Database::find($profile, $data['email_address'], 'email_address');
           
            if ($existingUser) {
                Response::error(409, "User already exists");
            }

            $userid = Utility::generate_uniqueId();      

            $userProfile = [
                'userid' => $userid,
                'fullname' => $data['fullname'],
                'email_address' => $data['email_address'],
                'phone' => $data['phone'] ?? '',
                'user_password' => password_hash($data['user_password'], PASSWORD_BCRYPT),               
                'address' =>  $data['address'] ?? '',
                'city' =>  $data['city'] ?? '',
                'city_state' => $data['city_state'] ?? 'enugu',               
                'avatar' => ''
            ];

            $userAccount = [
                'userid' => $userid,
                'status' => $data['status'] ?? 'active',
                'role_id' => $data['role_id'] ?? '1',
                'created_at'    => date('Y-m-d'),
            ];

            if (
                isset($_FILES['profileImage']) &&
                $_FILES['profileImage']['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['profileImage']['tmp_name'])
            ) {
                $target_dir =   "public/UPLOADS/avatar/";
                $avatar = Utility::uploadDocuments('profileImage', $target_dir);
                if (!$avatar || !$avatar['success']) Response::error(500, "Image upload failed");

                $userProfile['avatar'] = $avatar['files'][0];
            }

            if (
                Database::insert($profile, $userProfile) &&
                Database::insert($accounts, $userAccount)
            ) {

                ActivityService::saveActivity([
                    'userid' => $userid,
                    'type' => 'register',
                    'title' => 'registration successful',
                ]);
              
                EmailServices::registrationEmail($data);
                return ['success' => true, 'userid' => $userid];
            }
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'UserService::registerNewUser', ['host' => 'localhost'], $e);
            Response::error(500, "An error has occurred");
        }      
    }

     /**
     * Check if a user account is active.
     *
     * @param array|null $user
     * @return bool
     */
    private static function checkUserStatus(?array $user): bool
    {
        if (isset($user['status']) && $user['status'] !== 'active') {
            Response::error(401, "Account is not active");
        }

        return true;
    }

    public static function authenticate($email, $password){
            $profile = Utility::$profile_tbl;
            $accounts = Utility::$accounts;
        try {
            $user = Database::joinTables(
                $profile,
                [
                    [
                        "type" => "LEFT",
                        "table" => $accounts,
                        "on" => "$profile.userid = $accounts.userid"
                    ]
                ],
                ["$profile.*", "$accounts.*"],
                ["email_address" => $email]
            );


            if (empty($user) || !password_verify($password, $user[0]['user_password'])) {
                Response::error(401, "Invalid email or password");
            }

            $user = $user[0] ?? null;

            self::checkUserStatus($user);

            $token = AuthMiddleware::generateToken([
                'userid' => $user['userid'],
                'email' => $user['email_address'],
                'role' => $user['role_id'],
                'exp' => time() + 3600 //7200 seconds = 2 hours
            ]);

            $sessions_tbl = Utility::$sessions_tbl;
            $device       = Utility::getUserDevice();
            $ip           = Utility::getUserIP();

            $session = [
                'userid'        => $user['userid'],
                'session_token' => $token,
                'device'        => $device,
                'ip_address'    => $ip,
            ];

            Database::insert($sessions_tbl, $session);

            if (ActivityService::saveActivity([
                'userid' => $user['userid'],
                'type' => 'login',
                'title' => 'login successful',
            ])) {
                Response::success(['token' => $token], "Login successful");
            }
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'UserService::attemptLogin', ['host' => 'localhost'], $e);
            Response::error(500, "An error has occurred");
        }
    }

    public static function fetchAllUsers(){
        $profile = Utility::$profile_tbl;
        $accounts = Utility::$accounts;
        $roles = Utility::$roles;

        try {
            return Database::joinTables(
                "$profile u",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$accounts a",
                        "on" => "u.userid = a.userid"
                    ],
                    [
                        "type" => "LEFT",
                        "table" => "$roles r",
                        "on" => "a.role_id = r.id"
                    ],
                ],
                [
                    "u.*",
                    "a.status",
                    "a.role_id",
                    "a.created_at",
                    "a.reset_token",
                    "a.reset_token_expiration",
                    "r.role"
                ],
                [],
                ["order" => 'u.id DESC']

            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'AccountService::fetchUsersProfile', ['userid' => $_SESSION['userid']], $th);
            Response::error(500, "An error occurred while fetching users profiles");
        }
    }

    public static function fetchUserById($id){
        $profile = Utility::$profile_tbl;
        $accounts = Utility::$accounts;
        $roles = Utility::$roles;

        try {
            return Database::joinTables(
                "$profile u",
                [
                    [
                        "type" => "LEFT",
                        "table" => "$accounts a",
                        "on" => "u.userid = a.userid"
                    ],
                    [
                        "type" => "LEFT",
                        "table" => "$roles r",
                        "on" => "a.role_id = r.id"
                    ],

                ],
                [
                    "u.*",
                    "a.status",
                    "a.role_id",
                    "a.created_at",
                    "a.reset_token",
                    "a.reset_token_expiration",
                    "r.role"
                ],
                [
                    "OR" => [
                        "u.id" => $id,
                        "u.userid" => $id,
                        "u.email_address" => $id,
                    ]
                ],
                ["u.userid" => $id]
            );
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'UserService::fetchUserById', ['id' => $id], $th);
            Response::error(500, "An error occurred while fetching user details");
        }
    }

    public static function updateUser($id, $data, $user){
       $profile = Utility::$profile_tbl;
        $accounts = Utility::$accounts;
        try {         

            $profileInfo = [
                'fullname' => isset($data['fullname']) ? $data['fullname'] : $user['fullname'],
                'email_address' => isset($data['email_address']) ? $data['email_address'] : $user['email_address'],
                'user_password' => isset($data['user_password']) ? password_hash($data['user_password'], PASSWORD_BCRYPT) : $user['user_password'],
                'phone' => isset($data['phone']) ? $data['phone'] : $user['phone'],
                'address' => isset($data['address']) ? $data['address'] : $user['address'],
                'city' => isset($data['city']) ? $data['city'] : $user['city'],
                'city_state' => isset($data['city_state']) ? $data['city_state'] : $user['city_state'],
              
            ];

            $accountInfo = [
                'status' => isset($data['status']) ? $data['status'] : $user['status'],
                'role_id' => isset($data['role_id']) ? $data['role_id'] : $user['role_id'],
                'reset_token' => isset($data['reset_token']) ? $data['reset_token'] : $user['reset_token'],
                'reset_token_expiration' => isset($data['reset_token_expiration']) ? $data['reset_token_expiration'] : $user['reset_token_expiration'],
            ];

            if (
                isset($_FILES['profileImage']) &&
                $_FILES['profileImage']['error'] === UPLOAD_ERR_OK &&
                is_uploaded_file($_FILES['profileImage']['tmp_name'])
            ) {

                $target_dir =   "public/UPLOADS/avatar/";

                $user_avatar = Utility::uploadDocuments('profileImage', $target_dir);

                if (!$user_avatar || !$user_avatar['success']) Response::error(500, "Image upload failed");

                $profileInfo['avatar'] = $user_avatar['files'][0];

                if (isset($user['avatar'])) {
                    $filenameFromUrl = basename($user['avatar']);
                    $target_dir = "../public/UPLOADS/avatar/" . $filenameFromUrl;
                   
                    if (file_exists($target_dir))
                        unlink($target_dir);
                }
            }

            if (
                Database::update($profile,  $profileInfo, ['userid' => $id])
                && Database::update($accounts,  $accountInfo, ['userid' => $id])

            ) {
                ActivityService::saveActivity([
                    'userid' => $user['userid'],
                    'type' => 'update',
                    'title' => 'account update successful',
                ]);
                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'AccountService::updateUserInformation', [$id => $id], $th);
            Response::error(500, "An error occurred while updating user details");
        }
    }

    public static function updatePassword($user_id, $old_pw, $new_pw){
        // Logic to update user password
    }

    public static function logout($data)
    {
        try {

            header('Authorization: Bearer null');
            self::revokeSession($data['userid']);
            if (ActivityService::saveActivity([
                'userid' => $data['userid'],
                'type' => 'logout',
                'title' => 'logout successful',
            ])) return true;
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'AuthService::logout', ['host' => 'localhost'], $e);
            Response::error(500, "An error has occurred");
        }
    }

    /**
     * Revoke (delete) a user session by session ID.
     *
     * @param mixed $sessionId
     * @return bool|null
     */
    public static function revokeSession($sessionId): ?bool
    {
        try {
            $session_tbl = Utility::$sessions_tbl;
            return Database::delete($session_tbl, ['userid' => $sessionId]);
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'AuthService::revokeSession', ['host' => 'localhost'], $e);
            Response::error(500, "An error has occurred while revoking session");
        }

        return null;
    }

    public static function initiatePasswordReset($data)
    {

        try {

            $existingUser = self::fetchUserById($data['email_address']);

            if (empty($existingUser)) {
                Response::error(404, "User not found");
                return;
            }

            $user = $existingUser[0];

          

            $token = bin2hex(random_bytes(32));

            date_default_timezone_set('UTC');
            $expiry = (new \DateTime('+2 hour'))->format('Y-m-d H:i:s');

            $data = [
                'reset_token' => $token,
                'reset_token_expiration' => $expiry,
            ];;


            if (self::updateUser($user['userid'], $data, $user)) {

                return EmailServices::passwordResetEmail([
                    'fullname' => $user['fullname'],
                    'email_address' => $user['email_address'],
                    'reset_token' => $token,
                ]);              
               
            }
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'UserService::recoverAccount', ['host' => 'localhost'], $e);
            Response::error(500, "An error has occurred");
        }
    }

    public static function resetPassword($data)
    {
        $profile = Utility::$profile_tbl;
        $accounts = Utility::$accounts;
        try {
            $validateToken = Database::find($accounts, $data['token'], 'reset_token');
            if (!$validateToken) Response::error(401, "Wrong token presented");
            $userProfile = [
                'user_password' => password_hash($data['new_password'], PASSWORD_BCRYPT),
            ];
            $userAccount = [
                'reset_token' => null,
                'reset_token_expiration' => null,
            ];
            if (
                Database::update($profile,  $userProfile, ["userid" => $validateToken['userid']])
                && Database::update($accounts,  $userAccount, ["userid" => $validateToken['userid']])
            ) {
                ActivityService::saveActivity([
                    'userid' => $validateToken['userid'],
                    'type' => 'update',
                    'title' => 'password reset successful',
                ]);
                return true;
            }
        } catch (\Throwable $e) {
            Utility::log($e->getMessage(), 'error', 'UserService::resetPassword', ['host' => 'localhost'], $e);
            Response::error(500, "An error has occurred");
        }
    }

    public static function deleteUserAccount($id,  $userProfile)
    {
        $profile = Utility::$profile_tbl;
        $accounts = Utility::$accounts;
        try {           

            if (isset($userProfile['avatar'])) {
                $filenameFromUrl = basename($userProfile['avatar']);
                $target_dir = "../public/UPLOADS/avatars/" . $filenameFromUrl;

                if (file_exists($target_dir)) {
                    unlink($target_dir);
                }
            }


            if (
                Database::delete($profile, ['userid' => $id])
                && Database::delete($accounts, ['userid' => $id])
            ) {
                ActivityService::saveActivity([
                    'userid' => $_SESSION['userid'],
                    'type' => 'delete',
                    'title' => 'account deleted',
                ]);
                return true;
            }
        } catch (\Throwable $th) {
            Utility::log($th->getMessage(), 'error', 'AccountService::deleteUserAccount', ['userid' => $id], $th);
            Response::error(500, "An error occurred while deleting user account");
        }
    }
}