<?php

/**
 * This class is a decomposition of the old htdocs/index.php.
 * It tries to do exactly what PT used to do in index.php
 * and in the files inside the /inc/ directory.
 * 
 * Starting point is "launch()"
 * 
 * The class can be extended for customization purposes.
 *
 */
abstract class PTLauncher
{
    private $_base_xml = 0;
    
    /**
     * central starting point.
     * to be called in htdocs/index.php
     */
    public function launch()
    {
        // load data in base.xml
        $this->loadBaseXML();
        
        // load essential framework libraries
        $this->loadFramework();
        
        $S = PSurveillance::get();
        
        $this->checkEnvironment();
        $this->loadConfiguration();
        $this->loadDefaults();
        
        PSurveillance::setPoint('base_loaded');
        
        $this->initSession();
        $this->initAutoload();
        
        PSurveillance::setPoint('loading_apps');
        
        $this->doPostHandling();
        
        // find an app and run it.
        $this->chooseAndRunApplication();
    }
    
    
    /**
     * not sure what exactly the base.xml does
     * but PT needs it.
     *
     */
    protected function loadBaseXML()
    {
        require_once SCRIPT_BASE.'inc/base.inc.php';
        $this->_base_xml = $B;
    }
    
    /**
     * this will indirectly load all necessary framework files.
     *
     */
    protected function loadFramework()
    {
        require_once SCRIPT_BASE.'lib/libs.php';
    }
    
    /**
     * check something.
     *
     */
    protected function checkEnvironment()
    {
        // example call of requiring extension "xsl"
        //if (!PPHP::assertExtension('xsl')) 
        //    die('XSL required!');
    }
    
    
    /**
     * load configuration from inc/config.inc.php,
     * which also sets a lot of global symbols.
     *
     */
    protected function loadConfiguration()
    {
        require_once SCRIPT_BASE.'inc/config.inc.php';
    }
    
    /**
     * again, PT needs it.
     * 
     */
    protected function loadDefaults()
    {
        $B = $this->_base_xml;
        
        // copied from defaults.inc.php
        
        // we don't need PPckup() and translate($request) anymore,
        // we have controllerClassname() instead.
        
        // suspended
        $susp = $B->x->query('/basedata/suspended');
        if ($susp->length > 0) {
            $env = PVars::getObj('env');
            if ($env->suspend_url) {
                header('Location: '.$env->suspend_url);
            } else {
                header('HTTP/1.1 403 Forbidden');
            }
            PPHP::PExit();
        }
        
        // debug?
        $debug = $B->x->query('/basedata/debug');
        if ($debug->length > 0) {
            PVars::register('debug', true);
            $build = str_replace(SCRIPT_BASE, '', BUILD_DIR);
            PVars::register('build', substr($build, 0, strlen($build) - 1));
        }
    }
    
    /**
     * do some things which are necessary to start
     * using the $_SESSION
     *
     */
    protected function initSession()
    {
        if (defined ('SESSION_NAME')) {
            ini_set ('session.name', SESSION_NAME);
        }
        ini_set ('session.use_trans_sid', 0);
        ini_set ('url_rewrite.tags', '');
        ini_set ('session.hash_bits_per_character', 6);
        ini_set ('session.hash_function', 1);
        session_start();
        if (empty ($_COOKIE[session_name ()]) ) {
            PVars::register('cookiesAccepted', false);
        } else {
            PVars::register('cookiesAccepted', true);
        }
        PVars::register('queries', 0);
    }
    
    /**
     * register classnames for autoload.
     * The associated filenames are stored in the build.xml and module.xml files.
     *
     */
    protected function initAutoload()
    {
        PSurveillance::setPoint('loading_modules');
        // load modules
        $Mod = PModules::get();
        $Mod->setModuleDir(SCRIPT_BASE.'modules');
        $Mod->loadModules();
        PSurveillance::setPoint('modules_loaded');
        
        $Apps = PApps::get();
        $Apps->build();
        // process includes
        $includes = $Apps->getIncludes();
        if ($includes) {
            foreach ($includes as $inc) {
                require_once $inc;
            }
        }
        PSurveillance::setPoint('apps_loaded');
    }
    
    /**
     * call the PT posthandler, which does something with $_POST requests.
     *
     */
    protected function doPostHandling()
    {
        // this line is enough to start the posthandler action.
        $PH = PPostHandler::get();
    }
    
    /**
     * choose a controller and call the index() function.
     * If necessary, flush the buffered output.
     */
    protected function chooseAndRunApplication()
    {
        
        if (!$classname = $this->controllerClassname()) {
            die ("can't find a controller!");
        }
        
        // set the default page title
        // this should happen before the applications can overwrite it.
        // TODO: maybe there's a better place for this.
        PVars::getObj('page')->title='BeWelcome';
        
        
        $App = new $classname;
        $App->index();
        
        // TODO: what's this??
        $Rox = new RoxController;
        $Rox->buildContent();
        
        if (PVars::getObj('page')->output_done) {
            // output already happened, or not planned
        } else {
            $D = new PDefaultController;
            $D->output();
        }
    }
    
    
    /**
     * get the controller classname
     *
     * @return classname of the controller that should be run
     */
    protected function controllerClassname()
    {
        $request = PRequest::get()->request;
        
        if (!isset($request[0])) $name = 0;
        else $name = $request[0];
        
        $name = $this->translate($name);
        if (!$classname = $this->findController($name)) {
            $classname = $this->getDefaultController(); 
        }
        return $classname;
    }
    
    /**
     * find the name of the controller to be called
     *
     * @param string $name first part of request
     * @return string controller classname
     */
    protected function findController($name)
    {
        if (!$name) {
            return 0;
        } else switch ($name) {
                // other cases can be added!
            default:
                if ($classname = PApps::getAppName($name)) return $classname;
                else return 0;
        }
    }
    
    /**
     * overwriting this allows to redirect requests
     *
     * @param string $name the first part of the request
     * @return string the translated first part of the request
     */
    protected function translate($name)
    {
        return $name;
        
        /*
         * example implementation could be
        
        $o = array(
            // add elements like this:
            // 'examplepage1' => 'examplepage2'
        );
        if (array_key_exists(strtolower($name), $o)) {
            return $o[strtolower($name)];
        }
        return $name;
        
         */
    }
    
    /**
     * Which controller should be started if no other controller is found?
     *
     * @return string classname of the default controller
     */
    protected function getDefaultController()
    {
        // default is to return the name of the PDefaultController
        return 'PDefaultController';
        
        /*
         * example implementation could be
        
        return 'RoxController';
        
         */
    }
    
    /**
     * die if something in the env is not ok
     */
    protected function checkEnv()
    {
        // by default, check nothing
        
        /*
         * example implementation could be
        
        //BW Rox needs the GD plugin
        if (!PPHP::assertExtension('gd')) {
            die('GD lib required!');
        }
        
         */
    }
}

?>