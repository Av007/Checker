<?php
/**
 * Status command
 *
 * @author     Vladimir Avdeev (avdeevvladimir@gmail.com)
 * @copyright  Copyright (c) 2015 PhpStorm, LLC.
 * @version    1.0.0: 14.01.15 14:56
 */

namespace Checker\Commands;

use Checker\Loader;
use Checker\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZendDiagnostics\Runner\Runner;
use ZendDiagnostics\Check;
use ZendDiagnostics\Runner\Reporter\BasicConsole;

/**
 * Class Status
 *
 * @package Check
 */
class Status extends Command
{
    const OUTPUT = 80;
    /** @var array $modules modules list */
    protected $modules = array();

    /**
     * @inheritdoc
     */
    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->modules = array();
    }

    protected function configure()
    {
        $this->setName('status')
             ->setDescription('Execute modules')
             ->addOption('modules', null, InputOption::VALUE_NONE, 'Modules list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*// Configure check runner
        $runner = new Runner();
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
        $results = $runner->run();*/

        new Loader();
        // Add checks

        foreach ($this->modules as $module) {
            echo '<pre>'; var_dump($module); die;

        }

        $output->writeln(str_repeat('+', $this::OUTPUT));
        $output->writeln(Helper::getLogo());
        $output->writeln(str_repeat('=', $this::OUTPUT));

        // Add console reporter
        //$runner->addReporter(new BasicConsole(self::OUTPUT, true));

        // Run all checks
        $runner->run();
    }
}
