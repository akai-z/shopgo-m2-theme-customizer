<?php
/**
 * Copyright © 2015 ShopGo. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace ShopGo\ThemeCustomizer\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use ShopGo\ThemeCustomizer\Model\Less;

/**
 * Generate custom LESS file command
 */
class GenerateLessCommand extends Command
{
    /**
     * Theme argument
     */
    const THEME_ARGUMENT = 'theme';

    /**
     * @var Less
     */
    private $_less;


    /**
     * @param Less $less
     */
    public function __construct(
        Less $less
    ) {
        parent::__construct();
        $this->_less = $less;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('theme-customizer:generate-less')
            ->setDescription('Generate custom LESS command')
            ->setDefinition([
                new InputArgument(
                    self::THEME_ARGUMENT,
                    InputArgument::REQUIRED,
                    'Theme'
                )
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $result = 'Could not run command';
        $theme  = $input->getArgument(self::THEME_ARGUMENT);

        if (!is_null($theme)) {
            $this->_less->setAreaCode('adminhtml');
            $this->_less->setTheme($theme);

            $less   = $this->_less->getFieldsCustomLessContent();
            $result = $this->_less->createCustomLessFile($less);

            $result = $result
                ? 'Custom LESS file has been generated successfully!'
                : 'Failed to generate custom LESS file';
        } else {
            throw new \InvalidArgumentException('Argument ' . self::THEME_ARGUMENT . ' is missing.');
        }

        $output->writeln('<info>' . $result . '</info>');
    }
}
