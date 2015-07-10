<?php namespace CupOfTea\TwoStream\Routing;

use Illuminate\Http\Request;
use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Console\AppNamespaceDetectorTrait as AppNamespaceDetector;

use Composer\Autoload\ClassLoader;

use CupOfTea\TwoStream\Exceptions\SyntaxErrorException;
use CupOfTea\TwoStream\Exceptions\CatchableFatalErrorException;

class Route extends LaravelRoute
{
    
    use AppNamespaceDetector;
    
    /**
     * Run the route action and return the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function run(Request $request)
    {
        $this->container = $this->container ?: new Container;
        
        try {
            if (!is_string($this->action['uses'])) {
                return $this->runCallable($request);
            }
            
            list($class, $method) = explode('@', $this->action['uses']);
            $cl = new ClassLoader;
            $cl->addPsr4($this->getAppNamespace(), app('path'));
            $file = $cl->findFile($class);
            $cmd = 'php -l ' . $file;
            
            if (strpos(exec($cmd, $output), 'No syntax errors detected') === false) {
                $route = array_get($this->action, 'as', $this->action['uses']);
                $error = head(array_filter($output));
                
                throw new SyntaxErrorException($route, $error);
            }
            
            set_error_handler([$this, 'error']);
            
            if ($this->customDispatcherIsBound()) {
                return $this->runWithCustomDispatcher($request);
            }
            
            return $this->runController($request);
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }
    
    public function error($errno, $errstr)
    {
        if ($errno === E_RECOVERABLE_ERROR) {
            $name = $this->current->getName();
            $route = $name ? $name : $this->current->getActionName();
            throw new CatchableFatalErrorException($route, $errstr);
            
            return true;
        }
    }
    
}
