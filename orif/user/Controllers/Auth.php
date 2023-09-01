<?php
/**
 * User Authentication
 *
 * @author      Orif (ViDi,HeMa)
 * @link        https://github.com/OrifInformatique
 * @copyright   Copyright (c), Orif (https://www.orif.ch)
 */
namespace User\Controllers;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use User\Models\User_model;
use CodeIgniter\HTTP\Response;

class Auth extends BaseController {

    /**
     * Constructor
     */
    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger): void
    {
        // Set Access level before calling parent constructor
        // Accessibility for all users to let visitors have access to authentication
        $this->access_level = "*";
        parent::initController($request, $response, $logger);
        
        // Load required helpers
        helper('form');

        // Load required services
        $this->validation = \Config\Services::validation();

        // Load required models
        $this->user_model = new User_model();

        $this->db = \Config\Database::connect();
        
    }

    function errorhandler($data) {
        $data['title'] = 'Azure error';
        echo $this->display_view('\User\errors\azureErrors', $data);
        exit();
    }

    /**
     * Login user and create session variables
     *
     * @return void
     */
    public function azure_login() {

        $client_id = getenv('CLIENT_ID');
        $client_secret = getenv('CLIENT_SECRET');
        $ad_tenant = getenv('TENANT_ID');
        $graphUserScopes = getenv('GRAPH_USER_SCOPES');
        $redirect_uri = getenv('REDIRECT_URI');
        
        // Authentication part begins
        if (!isset($_GET["code"]) and !isset($_GET["error"])) {
            
            // First stage of the authentication process
            $url = "https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/authorize?";
            $url .= "state=" . session_id();
            $url .= "&scope=" . $graphUserScopes;
            $url .= "&response_type=code";
            $url .= "&approval_prompt=auto";
            $url .= "&client_id=" . $client_id;
            $url .= "&redirect_uri=" . urlencode($redirect_uri);
            header("Location: " . $url);  // Redirection to Microsoft's login page

        // Second stage of the authentication process
        } elseif (isset($_GET["error"])) {

            $data['Exception'] = null;
            $this->errorhandler($data);

        } elseif (strcmp(session_id(), $_GET["state"]) == 0) {
            //Checking that the session_id matches to the state for security reasons
            
            //Verifying the received tokens with Azure and finalizing the authentication part
            $content = "grant_type=authorization_code";
            $content .= "&client_id=" . $client_id;
            $content .= "&redirect_uri=" . urlencode($redirect_uri);
            $content .= "&code=" . $_GET["code"];
            $content .= "&client_secret=" . urlencode($client_secret);
            $options = array(
                "http" => array(  //Use "http" even if you send the request with https
                "method"  => "POST",
                "header"  => "Content-Type: application/x-www-form-urlencoded\r\n" .
                    "Content-Length: " . strlen($content) . "\r\n",
                "content" => $content
                )
            );
            $context  = stream_context_create($options);

            // Special error handler to verify if "client secret" is still valid
            try {
                $json = file_get_contents("https://login.microsoftonline.com/" . $ad_tenant . "/oauth2/v2.0/token", false, $context);
            } catch (\Exception $e) {
                $data['title'] = 'Azure error';
                $data['Exception'] = $e;
                echo $this->display_view('\User\errors\401error', $data);
                exit();
            };

            if ($json === false){
                //Error received during Bearer token fetch
                $data['Exception'] = lang('user_lang.msg_err_azure_no_token').'.';
                $this->errorhandler($data);
            };
            $authdata = json_decode($json, true);
            if (isset($authdata["error"])){
                //Bearer token fetch contained an error
                $data['Exception'] = null;
                $this->errorhandler($data);
            };
            
            //Fetching user information
            $options = array(
                "http" => array(  //Use "http" even if you send the request with https
                "method" => "GET",
                "header" => "Accept: application/json\r\n" .
                "Authorization: Bearer " . $authdata["access_token"] . "\r\n"
                )
            );
            $context = stream_context_create($options);
            $json = file_get_contents("https://graph.microsoft.com/v1.0/me", false, $context);
            if ($json === false) {
                // Error received during user data fetch.
                $data['Exception'] = null;
                $this->errorhandler($data);
            };
            $userdata = json_decode($json, true);
            if (isset($userdata["error"])) {
                // User data fetch contained an error.
                $data['Exception'] = null;
                $this->errorhandler($data);
            };

            // Setting up the session
            $user_email = $userdata["mail"];

            $_SESSION['logged_in'] = (bool)true;
            $_SESSION['azure_identification'] = (bool)true;
            
            $ci_user = $this->user_model->where('azure_mail', $user_email)->first();
                
            if (isset($ci_user['azure_mail'])) { 
                // if email is registered in DB, get personnal user informations
                $_SESSION['user_access'] = (int)$this->user_model->get_access_level($ci_user);
                $_SESSION['username'] = $ci_user["username"];
                $_SESSION['user_id'] = (int)$ci_user['id'];
            } else {
                // if email is not registered in DB, use default azure informations
                $_SESSION['user_access'] = config("\User\Config\UserConfig")->azure_default_access_lvl;
                $_SESSION['username'] = $userdata["displayName"];
                $_SESSION['user_id'] = NULL;
            }
            // Send the user to the redirection URL
            return redirect()->to($_SESSION['after_login_redirect']);

        } else {
            // Returned states mismatch and no $_GET["error"] received.
            $data['Exception'] = lang('user_lang.msg_err_azure_mismatch').'.';
            $this->errorhandler($data);
        }
    }

    public function login(): string|Response
    {
        // If user is not already logged
        if(!(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true)) {

            // Store the redirection URL in a session variable
            if (!is_null($this->request->getVar('after_login_redirect'))) {
                $_SESSION['after_login_redirect'] = $this->request->getVar('after_login_redirect');
            }
            // If no redirection URL is provided or the redirection URL is the
            // login form, redirect to site's root after login
            if (!isset($_SESSION['after_login_redirect'])
                    || $_SESSION['after_login_redirect'] == current_url()) {

                $_SESSION['after_login_redirect'] = base_url();
            }

            // Check if the form has been submitted, else check if Microsoft button submitted
            if (!is_null($this->request->getVar('btn_login'))) {

                // Define fields validation rules
                $validation_rules=[
                    'username'=>[
                    'label' => 'user_lang.field_username',
                    'rules' => 'trim|required|'
                        . 'min_length['.config("\User\Config\UserConfig")->username_min_length.']|'
                        . 'max_length['.config("\User\Config\UserConfig")->username_max_length.']'],
                    'password'=>[
                        'label' => 'user_lang.field_password',
                        'rules' => 'trim|required|'
                            . 'min_length['.config("\User\Config\UserConfig")->password_min_length.']|'
                            . 'max_length['.config("\User\Config\UserConfig")->password_max_length.']'
                    ]
                ];
                $this->validation->setRules($validation_rules);

                // Check fields validation rules
                if ($this->validation->withRequest($this->request)->run() == true) {
                    $input = $this->request->getVar('username');
                    $password = $this->request->getvar('password');
                    $ismail = $this->user_model->check_password_email($input, $password);
                    if ($ismail || $this->user_model->check_password_name($input, $password)) {
                        // Login success
                        $user = NULL;
                        // User is either logging in through an email or an username
                        // Even if an username is entered like an email, we're not grabbing it
                        if ($ismail) {
                            $user = $this->user_model->getWhere(['email'=>$input])->getRow();
                        } else {
                            $user = $this->user_model->getWhere(['username'=>$input])->getRow();
                        }
                
                        $_SESSION['user_id'] = (int)$user->id;
                        $_SESSION['username'] = (string)$user->username;
                        $_SESSION['user_access'] = (int)$this->user_model->get_access_level($user);
                        $_SESSION['logged_in'] = (bool)true;

                        // Send the user to the redirection URL
                        return redirect()->to($_SESSION['after_login_redirect']);

                    } else {
                        // Login failed
                        $this->session->setFlashdata('message-danger', lang('user_lang.msg_err_invalid_password'));
                    }
                    $this->session->setFlashdata('message-danger', lang('user_lang.msg_err_invalid_password'));
                }

            // Check if microsoft login button submitted, else, display login page
            } else if (!is_null($this->request->getPost('btn_login_microsoft'))) {
                $this->azure_login();
                exit();
            }
            //Display login page
            $output = array('title' => lang('user_lang.title_page_login'));
            return $this->display_view('\User\auth\login', $output);
        } else {
            return redirect()->to(base_url());
        }
    }
    
    /**
     * Logout and destroy session
     *
     * @return void
     */
    public function logout(): Response
    {
        // Restart session with empty parameters
        $_SESSION = [];
        session_reset();
        session_unset();

        return redirect()->to(base_url());
    }

    /**
     * Displays a form to let user change his password
     *
     * @return void
     */
    public function change_password(): Response|string 
    {
        // Check if access is allowed
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {

            // Get user from DB, redirect if user doesn't exist
            $user = $this->user_model->withDeleted()->find($_SESSION['user_id']);
            if (is_null($user)) return redirect()->to('/user/auth/login');

            // Empty errors message in output
            $output['errors'] = [];
            
            // Check if the form has been submitted, else just display the form
            if (!is_null($this->request->getVar('btn_change_password'))) {
                $old_password = $this->request->getVar('old_password');

                if($this->user_model->check_password_name($user['username'], $old_password)) {
                    $user['password'] = $this->request->getVar('new_password');
                    $user['password_confirm'] = $this->request->getVar('confirm_password');

                    $this->user_model->update($user['id'], $user);

                    if ($this->user_model->errors()==null) {
                        // No error happened, redirect
                        return redirect()->to(base_url());
                    } else {
                        // Display error messages
                        $output['errors'] = $this->user_model->errors();
                    }

                } else {
                    // Old password error
                    $output['errors'][] = lang('user_lang.msg_err_invalid_old_password');
                }
            }

            // Display the password change form
            $output['title'] = lang('user_lang.page_my_password_change');
            return $this->display_view('\User\auth\change_password', $output);

        } else {
            // Access is not allowed
            return redirect()->to('/user/auth/login');
        }
    }
}