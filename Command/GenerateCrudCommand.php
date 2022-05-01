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
use Igoooor\ApiGeneratorBundle\Generator\Configuration\ConfigureCrud;
use Igoooor\ApiGeneratorBundle\Generator\Configuration\ConfigureEntity;
use Igoooor\ApiGeneratorBundle\Generator\CrudGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class GenerateCrudCommand
 */
class GenerateCrudCommand extends Command
{
    protected static $defaultName = 'igoooor:generate:crud';
    /**
     * @var CrudGenerator
     */
    private CrudGenerator $generator;
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
     * @var ConfigureCrud
     */
    private ConfigureCrud $configureCrud;

    /**
     * GenerateCrudCommand constructor.
     *
     * @param CrudGenerator       $generator
     * @param ConfigureBundle     $configureBundle
     * @param ConfigureEntity     $configureEntity
     * @param ConfigureController $configureController
     * @param ConfigureCrud       $configureCrud
     */
    public function __construct(CrudGenerator $generator, ConfigureBundle $configureBundle, ConfigureEntity $configureEntity, ConfigureController $configureController, ConfigureCrud $configureCrud)
    {
        parent::__construct();
        $this->generator = $generator;
        $this->configureBundle = $configureBundle;
        $this->configureEntity = $configureEntity;
        $this->configureController = $configureController;
        $this->configureCrud = $configureCrud;
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
        $this->configureEntity->configure($io, $config);
        $this->configureController->configure($io, $config);

        $configurationMode = $io->choice('Choose a configuration', ['minimal', 'full', 'customized'], 'minimal');
        if ('full' === $configurationMode) {
            $config->overrides = [
                'findAll' => true,
                'getSerializerGroups' => true,
                'getEntity' => true,
                'createEntity' => true,
                'persistEntity' => true,
                'updateEntity' => true,
                'deleteEntity' => true,
            ];
            $config->crudEvents = [
                'PreCreatedEvent' => true,
                'PostCreatedEvent' => true,
                'PreUpdatedEvent' => true,
                'PostUpdatedEvent' => true,
                'PreDeletedEvent' => true,
                'PostDeletedEvent' => true,
            ];
            $config->roles = [
                'list' => $config->defaultRole,
                'new' => 'ROLE_USER',
                'detail' => 'ROLE_USER',
                'update' => 'ROLE_USER',
                'delete' => 'ROLE_USER',
            ];
        } else if ('customized' === $configurationMode) {
            $this->configureCrud->configure($io, $config);
        }

        $this->generator->generate($config);

        return 0;
    }
}
