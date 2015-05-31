<?php
namespace Checker;

use DirectoryIterator;
use RuntimeException;
use ZendDiagnostics\Check\Callback;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Runner\Runner;

class Loader
{
    /** @var array $modules configuration modules */
    protected $modules = array();

    public function __construct()
    {
        $directory = dirname(__FILE__) . '/Module';

        foreach (new DirectoryIterator($directory) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $this->modules[] = $this->run(basename($fileInfo->getFilename(), '.' . $fileInfo->getExtension()));
        }

        // Configure check runner
        /*$runner = new Runner();
        $runner->addChecks($checkCollection);
        $runner->getConfig()->setBreakOnFailure($breakOnFailure);
        if (!$quiet && $this->getRequest() instanceof ConsoleRequest) {
            if ($verbose || $debug) {
                $runner->addReporter(new VerboseConsole($console, $debug));
            } else {
                $runner->addReporter(new BasicConsole($console));
            }
        }
        // Run tests
        $results = $runner->run();
        // Return result
        if ($this->getRequest() instanceof ConsoleRequest) {
            // Return appropriate error code in console
            $model = new ConsoleModel();
            $model->setVariable('results', $results);
            if ($results->getFailureCount() > 0) {
                $model->setErrorLevel(1);
            } else {
                $model->setErrorLevel(0);
            }
        } else {
            // Display results as a web page
            $model = new ViewModel();
            $model->setVariable('results', $results);
        }
        return $model;*/

    }

    /**
     * @param $moduleName
     * @param array $params
     * @return object|Callback
     */
    protected function run($moduleName, $params = array())
    {
        if (is_string($moduleName) && class_exists('Checker\\Module\\' . $moduleName)) {
            $class = new \ReflectionClass('ZendDiagnostics\\Check\\' . $moduleName);
            $check = $class->newInstanceArgs($params);
            // Try to use the ZFTool namespace
        } elseif (is_string($moduleName) && class_exists('ZendDiagnostics\\Check\\' . $moduleName)) {
            $class = new \ReflectionClass('ZFTool\\Diagnostics\\Check\\' . $moduleName);
            $check = $class->newInstanceArgs($params);
            // Check if provided with a callable inside an array
        } elseif (is_callable($moduleName)) {
            $check = new Callback($moduleName, $params);
            // Try to expand check using class name
        } elseif (is_string($moduleName) && class_exists($moduleName)) {
            $class = new \ReflectionClass($moduleName);
            $check = $class->newInstanceArgs($params);
        } else {
            throw new RuntimeException(
                'Cannot find check class or service with the name of "' . $moduleName . '"'
            );
        }

        return $check;
    }

}
