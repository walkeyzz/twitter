<?php

namespace App\Controllers\Frontend;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */

    public $logged_user = null;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->comments = new \App\Models\Comments();
    		$this->follow = new \App\Models\Follow();
    		$this->likes = new \App\Models\Likes();
    		$this->Messages = new \App\Models\Messages();
    		$this->notification = new \App\Models\Notification();
    		$this->trends = new \App\Models\Trends();
    		$this->tweets = new \App\Models\Tweets();
    		$this->users = new \App\Models\Users();

        $this->validation = \Config\Services::validation();
        $this->session = \Config\Services::session();
        // Load information our frontend will need -- Create our data array
    		$this->data = [
    			'logged_user' => $this->logged_user,
    		];

        // E.g.: $this->session = \Config\Services::session();
    }
}
