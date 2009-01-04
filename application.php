<?php

class ZfApplication
{
	/**
	 * The environment state of your current application
	 *
	 * @var string
	 */
	protected $_environment;

	/**
	 * Sets the environment to load from configuration file
	 *
	 * @param string $environment - The environment to set
	 * @return void
	 */
	public function setEnvironment($environment)
	{
		$this->_environment = $environment;
	}

	/**
	 * Returns the environment which is currently set
	 *
	 * @return string
	 */
	public function getEnvironment()
	{
		return $this->_environment;
	}

	private function auth()
	{
	    $auth = Zend_Auth::getInstance();
	    $auth->setStorage(new Zend_Auth_Storage_Session('authNamespace'));
	    $result = $auth->authenticate($authAdapter);
	}
	/**
	 * Convenience method to bootstrap the application
	 *
	 * @return mixed
	 */
	public function bootstrap()
	{
		if (! $this->_environment) {
			throw new Exception ( 'Please set the environment using ::setEnvironment' );
		}
		$frontController = $this->initialize ();
		$frontController->getRouter()->addRoute('requestVars', new Gam_Controller_Router_Route_RequestVars());


		$this->setupRoutes ( $frontController );
		$response = $this->dispatch ( $frontController );

		$this->render ( $response );
	}
	

	/**
	 * Initialization stage, loads configration files, sets up includes paths
	 * and instantiazed the frontController
	 *
	 * @return Zend_Controller_Front
	 */
	public function initialize()
	{
		// Set the include path
		set_include_path ( dirname ( __FILE__ ) . '/library' . PATH_SEPARATOR . get_include_path () );

		/* Zend_Loader */
		require_once 'Zend/Loader.php';
			
		/* Zend_Registry */
		require_once 'Zend/Registry.php';

		/* Zend_Config_ini */
		require_once 'Zend/Config/Ini.php';
        
		/* Zend_Db */
		require_once 'Zend/Db.php';
		
		/* Zend_Controller_Front */
		require_once 'Zend/Controller/Front.php';

		/* Zend_Controller_Router_Rewrite */
		require_once 'Zend/Controller/Router/Rewrite.php';
		
		/* Zend_Db_Table_Abstract */
		require_once 'Zend/Db/Table/Abstract.php';
		
		/* Zend_Acl */
		require_once 'Zend/Acl.php';
		
		/* Zend_Acl_Role */
		require_once 'Zend/Acl/Role.php';
		
		/* Ris_Controller_Router_Route_RequestVars */
		//require_once 'Gam/Controller/Router/Route/RequestVars.php';
		
		/*
		 * Load the given stage from our configuration file,
		 * and store it into the registry for later usage.
		 */
		$config = new Zend_Config_Ini ( dirname ( __FILE__ ) . '/app/etc/config.ini', $this->getEnvironment () );
		Zend_Registry::set ( 'config', $config );
		define('GamBASEPATH', dirname ( __FILE__ ));
		switch ($config->db) {
		    case 'Pdo_Sqlite':
		        $db = Zend_Db::factory('Pdo_Sqlite', array(
                    'dbname' => GamBASEPATH . $config->sqlite->dbHost
                    ));
		        break;
		}
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
        Zend_Registry::set ( 'config', $config );
        Zend_Registry::set ( 'db', $db );
        
        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role('guest'))
            ->addRole(new Zend_Acl_Role('member'))
            ->addRole(new Zend_Acl_Role('admin'));
            
        $parents = array('guest', 'member', 'admin');    
        $acl->addRole(new Zend_Acl_Role('gonzalo'), $parents);   
         
        Zend_Registry::set ( 'acl', $acl );
		/*
		 * Create an instance of the frontcontroller, and point it to our
		 * controller directory
		 */
		$frontController = Zend_Controller_Front::getInstance ();
		$frontController->throwExceptions ( ( bool ) $config->mvc->exceptions );

		foreach ( $config->modules as $module => $folder ) {
			$frontController->addControllerDirectory ( dirname ( __FILE__ ) . "$folder", $module );
		}

		$frontController->setParam('noErrorHandler', true);
		//$frontController->setParam('noViewRenderer', true);
		Zend_Controller_Action_HelperBroker::addPrefix('Gam_Action_Helper');

		Zend_Loader::registerAutoload();
		//$frontController->setControllerDirectory;
		return $frontController;
	}

	/**
	 * Sets up the custom routes
	 *
	 * @param  object Zend_Controller_Front $frontController - The frontcontroller
	 * @return object Zend_Controller_Router_Rewrite
	 */
	public function setupRoutes(Zend_Controller_Front $frontController)
	{
		// Retrieve the router from the frontcontroller
		$router = $frontController->getRouter ();

		/*
		 * You can add routes here like so:
		 * $router->addRoute(
		 *    'home',
		 *    new Zend_Controller_Router_Route('home', array(
		 *        'controller' => 'index',
		 *        'action'     => 'index'
		 *    ))
		 * );
		 */

		return $router;
	}

	/**
	 * Dispatches the request
	 *
	 * @param  object Zend_Controller_Front $frontController - The frontcontroller
	 * @return object Zend_Controller_Response_Abstract
	 */
	public function dispatch(Zend_Controller_Front $frontController)
	{
		// Return the response
		$frontController->returnResponse ( true );
		return $frontController->dispatch ();
	}

	/**
	 * Renders the response
	 *
	 * @param  object Zend_Controller_Response_Abstract $response - The response object
	 * @return void
	 */
	public function render(Zend_Controller_Response_Abstract $response)
	{
		$response->sendHeaders ();
		$response->outputBody ();
	}
}
