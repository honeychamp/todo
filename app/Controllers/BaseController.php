<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        $this->helpers = ['url', 'form', 'app'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');

        // Validate user session against database to handle database resets/deleted users
        if (session()->get('logged_in') && session()->get('user_id')) {
            try {
                $userModel = new \App\Models\UserModel();
                if (!$userModel->find(session()->get('user_id'))) {
                    session()->remove(['logged_in', 'user_id', 'username']);
                }
            } catch (\Exception $e) {
                // If the table doesn't exist at all (e.g. migrate:refresh)
                session()->remove(['logged_in', 'user_id', 'username']);
            }
        }
    }
}
