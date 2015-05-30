<?php
namespace Checker;

use DirectoryIterator;
use RuntimeException;
use ZendDiagnostics\Check\Callback;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Runner\Runner;

// https://github.com/zendframework/ZFTool/blob/master/src/ZFTool/Controller/DiagnosticsController.php
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

            $this->modules[] = array('Checker\\Module' => basename($fileInfo->getFilename(), '.' . $fileInfo->getExtension()));
        }

        // Collect diagnosis tests from modules
        foreach ($this->modules as $moduleName => $module) {
            if (is_callable(array($module, 'getDiagnostics'))) {
                $checks = $module->getDiagnostics();
                if (is_array($checks)) {
                    $config[$moduleName] = $checks;
                }
            }
        }

    }

    public function build()
    {
        foreach ($config as $checkGroupName => $checks) {
            foreach ($checks as $checkLabel => $check) {
                // Do not use numeric labels.
                if (!$checkLabel || is_numeric($checkLabel)) {
                    $checkLabel = false;
                }
                // Handle a callable.
                if (is_callable($check)) {
                    $check = new Callback($check);
                    if ($checkLabel) {
                        $check->setLabel($checkGroupName . ': ' . $checkLabel);
                    }
                    $checkCollection[] = $check;
                    continue;
                }
                // Handle check object instance.
                if (is_object($check)) {
                    if (!$check instanceof CheckInterface) {
                        throw new RuntimeException(
                            'Cannot use object of class "' . get_class($check). '" as check. '.
                            'Expected instance of ZendDiagnostics\Check\CheckInterface'
                        );
                    }
                    // Use duck-typing for determining if the check allows for setting custom label
                    if ($checkLabel && is_callable(array($check, 'setLabel'))) {
                        $check->setLabel($checkGroupName . ': ' . $checkLabel);
                    }
                    $checkCollection[] = $check;
                    continue;
                }
                // Handle an array containing callback or identifier with optional parameters.
                if (is_array($check)) {
                    if (!count($check)) {
                        throw new RuntimeException(
                            'Cannot use an empty array() as check definition in "'.$checkGroupName.'"'
                        );
                    }
                    // extract check identifier and store the remainder of array as parameters
                    $testName = array_shift($check);
                    $params = $check;
                } elseif (is_scalar($check)) {
                    $testName = $check;
                    $params = array();
                } else {
                    throw new RuntimeException(
                        'Cannot understand diagnostic check definition "' . gettype($check). '" in "'.$checkGroupName.'"'
                    );
                }
                if (is_string($testName) && class_exists('ZendDiagnostics\\Check\\' . $testName)) {
                    $class = new \ReflectionClass('ZendDiagnostics\\Check\\' . $testName);
                    $check = $class->newInstanceArgs($params);
                    // Try to use the ZFTool namespace
                } elseif (is_string($testName) && class_exists('ZFTool\\Diagnostics\\Check\\' . $testName)) {
                    $class = new \ReflectionClass('ZFTool\\Diagnostics\\Check\\' . $testName);
                    $check = $class->newInstanceArgs($params);
                    // Check if provided with a callable inside an array
                } elseif (is_callable($testName)) {
                    $check = new Callback($testName, $params);
                    if ($checkLabel) {
                        $check->setLabel($checkGroupName . ': ' . $checkLabel);
                    }
                    $checkCollection[] = $check;
                    continue;
                    // Try to expand check using class name
                } elseif (is_string($testName) && class_exists($testName)) {
                    $class = new \ReflectionClass($testName);
                    $check = $class->newInstanceArgs($params);
                } else {
                    throw new RuntimeException(
                        'Cannot find check class or service with the name of "' . $testName . '" ('.$checkGroupName.')'
                    );
                }
                if (!$check instanceof CheckInterface) {
                    // not a real check
                    throw new RuntimeException(
                        'The check object of class '.get_class($check).' does not implement '.
                        'ZendDiagnostics\Check\CheckInterface'
                    );
                }
                // Use duck-typing for determining if the check allows for setting custom label
                if ($checkLabel && is_callable(array($check, 'setLabel'))) {
                    $check->setLabel($checkGroupName . ': ' . $checkLabel);
                }
                $checkCollection[] = $check;
            }
        }
    }
}
