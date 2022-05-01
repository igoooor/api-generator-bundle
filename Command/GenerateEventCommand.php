<?php
/**
 * Created by PhpStorm.
 * User: igorweigel
 * Date: 19.05.2020
 * Time: 20:12
 */

namespace Igoooor\ApiGeneratorBundle\Command;

use Igoooor\ApiGeneratorBundle\Generator\Configuration\Configuration;
use Igoooor\ApiGeneratorBundle\Generator\Configuration\ConfigureBundle;
use Igoooor\ApiGeneratorBundle\Generator\Configuration\ConfigureController;
use Igoooor\ApiGeneratorBundle\Generator\Configuration\ConfigureCrudEvent;
use Igoooor\ApiGeneratorBundle\Generator\Configuration\ConfigureEntity;
use Igoooor\ApiGeneratorBundle\Generator\CrudEventGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GenerateEventCommand
 */
class GenerateEventCommand extends Command
{
    protected static $defaultName = 'igoooor:generate:event';
    /**
     * @var CrudEventGenerator
     */
    private CrudEventGenerator $generator;
    /**
     * @var ConfigureBundle
     */
    private ConfigureBundle $configureBundle;
    /**
     * @var ConfigureEntity
     */
    private ConfigureEntity $configureEntity;
    /**
     * @var ConfigureController
     */
    private ConfigureController $configureController;
    /**
     * @var ConfigureCrudEvent
     */
    private ConfigureCrudEvent $configureCrudEvent;

    /**
     * GenerateEventCommand constructor.
     *
     * @param CrudEventGenerator  $generator
     * @param ConfigureBundle     $configureBundle
     * @param ConfigureEntity     $configureEntity
     * @param ConfigureController $configureController
     * @param ConfigureCrudEvent  $configureCrudEvent
     */
    public function __construct(CrudEventGenerator $generator, ConfigureBundle $configureBundle, ConfigureEntity $configureEntity, ConfigureController $configureController, ConfigureCrudEvent $configureCrudEvent)
    {
        parent::__construct();
        $this->generator = $generator;
        $this->configureBundle = $configureBundle;
        $this->configureEntity = $configureEntity;
        $this->configureController = $configureController;
        $this->configureCrudEvent = $configureCrudEvent;
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     *
     * @throws \ReflectionException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = new Configuration();
        $io = new SymfonyStyle($input, $output);
        $this->configureBundle->configure($io, $config);
        $this->configureEntity->configure($io, $config, false);
        $this->configureController->configure($io, $config, false);
        $this->configureCrudEvent->configure($io, $config, true);

        $this->generator->generate($config);

        return 0;
    }
}
