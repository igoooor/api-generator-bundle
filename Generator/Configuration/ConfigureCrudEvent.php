<?php
/**
 * Created by PhpStorm.
 * User: igorweigel
 * Date: 07.10.2020
 * Time: 22:11
 */

namespace Igoooor\ApiGeneratorBundle\Generator\Configuration;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ConfigureCrudEvent
 */
class ConfigureCrudEvent
{
    /**
     * @param SymfonyStyle  $io
     * @param Configuration $config
     * @param bool          $skipQuestion
     */
    public function configure(SymfonyStyle $io, Configuration $config, bool $skipQuestion = false)
    {
        $io->section('CRUD Events');
        if ($skipQuestion || $io->confirm('Do you need some CRUD events?', false)) {
            foreach ($config->crudEvents as $crudEvent => $value) {
                $config->crudEvents[$crudEvent] = $io->confirm($crudEvent, $config->crudEvents[$crudEvent]);
            }
        }
    }
}
