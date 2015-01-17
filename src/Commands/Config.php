<?php
/**
 * Config command
 *
 1
 * @copyright  Copyright (c) 2015 PhpStorm, LLC.
 * @version    1.0.0: 14.01.15 14:56
 */

namespace Checker\Commands;

use Checker\Factory;
use Checker\Helper;
use Checker\Modules;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExtractConfig
 *
 * @package Check
 */
class Config extends Command
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
    }

    protected function configure()
    {
        $this
            ->setName('config')
            ->setDescription('Extract Speakaboos configuration')
            ->addArgument('name', InputArgument::OPTIONAL, 'Configuration name')
            ->addArgument('value', InputArgument::OPTIONAL, 'Configuration value')
            ->addOption('list', null, InputOption::VALUE_NONE, 'Configuration sections')
            ->addOption('show', null, InputOption::VALUE_NONE, 'Configuration show item')
            ->addOption('set', null, InputOption::VALUE_NONE, 'Configuration update value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $formatter = $formatter = $this->getHelper('formatter');
        // sets styles
        foreach ($this->styles as $key => $style) {
            $output->getFormatter()->setStyle($key, $style);
        }

        $output->writeln(str_repeat('+', $this::OUTPUT));
        $output->writeln(Helper::getLogo());
        $output->writeln(str_repeat('=', $this::OUTPUT));

        if ($input->getOption('list')) {
            $content = Helper::showItemConfig();
            $content = implode(', ', $content);

        } else if($input->getOption('show')) {
            if (!$name = $input->getArgument('name')) {
                $errorMessages = array('Error!', 'Required parameter: name');
                $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
                $output->writeln($formattedBlock);

                return;
            }

            $content = Helper::extractConfig($name);
        } else if($input->getOption('set')) {
            if (!$name = $input->getArgument('name')) {
                $errorMessages = array('Error!', 'Required parameter: name');
                $formattedBlock = $formatter->formatBlock($errorMessages, 'error');
                $output->writeln($formattedBlock);

                return;
            }

            $content = Helper::updateConfig($name, $input->getArgument('value'));
            $content = $content ? 'Updated' : 'Failed';
        } else {
            $content = Helper::extractConfig();
        }

        $output->writeln('<info>' . $content . '</info>');
    }
}
