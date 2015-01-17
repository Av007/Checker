<?php
/**
 * Status command
 *
 * @author     Vladimir Avdeev (avdeevvladimir@gmail.com)
 * @copyright  Copyright (c) 2015 PhpStorm, LLC.
 * @version    1.0.0: 14.01.15 14:56
 */

namespace Checker\Commands;

use Checker\Factory;
use Checker\Helper;
use Checker\Modules;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Status
 *
 * @package Check
 */
class Status extends Command
{
    const OUTPUT = 70;
    /** @var array $styles custom styles */
    protected $styles = array();
    /** @var array $modules modules list */
    protected $modules = array();

    /**
     * @inheritdoc
     */
    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->styles['fire'] = new OutputFormatterStyle('white', 'green', array('bold'));
        $this->modules = array(
            'database',
            //'email',
            'memcached',
            'pages',
        );
    }

    protected function configure()
    {
        $this
            ->setName('status')
            ->setDescription('Execute modules')
            ->addOption('modules', null, InputOption::VALUE_NONE, 'Modules list');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // sets styles
        foreach ($this->styles as $key => $style) {
            $output->getFormatter()->setStyle($key, $style);
        }

        $output->writeln(str_repeat('+', $this::OUTPUT));
        $output->writeln(Helper::getLogo());
        $output->writeln(str_repeat('=', $this::OUTPUT));

        if ($input->getOption('modules')) {
            foreach ($this->modules as $module) {
                $output->writeln('<info>' . $module . '</info>');
            }
            return;
        }

        foreach ($this->modules as $module) {
            $currentModule = Factory::build($module);

            $initData = $currentModule->init();
            $output->write('<fire>' . $initData['title'] . '</fire>' . '<info> ' . $initData['operation'] . '</info>: ');
            $data = $currentModule->run();
            $output->writeln('<comment>' . $data['result'] . '</comment>');
        }
    }
}
