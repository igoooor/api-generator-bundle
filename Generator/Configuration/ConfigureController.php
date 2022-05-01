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
 * Class ConfigureController
 */
class ConfigureController
{
    /**
     * @param SymfonyStyle  $io
     * @param Configuration $config
     * @param bool          $full
     */
    public function configure(SymfonyStyle $io, Configuration $config, bool $full = true)
    {
        $notBlank = function ($val) {
            if (empty($val)) {
                throw new \RuntimeException('Can not be blank.');
            }

            return $val;
        };

        $io->title('Controller configuration');
        $config->controllerFqcn = sprintf(
            '%s\%sController',
            str_replace('Entity', 'Controller', $config->entityNamespace),
            $config->entityName
        );
        $config->controllerFqcn = $io->ask('Fqcn?', $config->controllerFqcn, $notBlank);
        $config->controllerNamespace = substr($config->controllerFqcn, 0, strrpos($config->controllerFqcn, '\\'));
        $config->controllerName = substr($config->controllerFqcn, strrpos($config->controllerFqcn, '\\') + 1);
        $config->controllerPath = str_replace(array_key_first($config->bundle), reset($config->bundle), $config->controllerNamespace);

        if ($full) {
            $config->routePath = $io->ask('Route Path? /api/', $this->camelToKebab($config->entityName), $notBlank);
            $config->routeName = $io->ask('Route Name?', $this->camelToSnake($config->entityName), $notBlank);
        }
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function camelToSnake(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private function camelToKebab(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('-', $ret);
    }
}
