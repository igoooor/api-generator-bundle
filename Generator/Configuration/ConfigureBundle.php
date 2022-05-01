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
 * Class ConfigureBundle
 */
class ConfigureBundle
{
    private string $projectDir;

    /**
     * ConfigureBundle constructor.
     *
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * @param SymfonyStyle  $io
     * @param Configuration $config
     */
    public function configure(SymfonyStyle $io, Configuration $config)
    {
        $notBlank = function ($val) {
            if (empty($val)) {
                throw new \RuntimeException('Can not be blank.');
            }

            return $val;
        };

        $displayTitle = true;
        $config->projectDir = $this->projectDir;
        if (!is_dir($config->projectDir)) {
            $io->title('Bundle configuration');
            $displayTitle = false;
            $config->projectDir = $io->ask('projectDir ?', '/app/docroot', function ($val) {
                if (!is_dir($val)) {
                    throw new \RuntimeException('Directory not found.');
                }

                return $val;
            });
        }
        $config->composerFile = $config->projectDir.'/composer.json';
        if (!file_exists($config->composerFile)) {
            if ($displayTitle) {
                $io->title('Bundle configuration');
                $displayTitle = false;
            }
            $config->composerFile = $io->ask('composer.json path?', '', function ($val) {
                if (!file_exists($val)) {
                    throw new \RuntimeException('File not found.');
                }

                return $val;
            });
        }
        $composer = json_decode(file_get_contents($config->composerFile), true);
        if (array_key_exists('autoload', $composer) && array_key_exists('psr-4', $composer['autoload'])) {
            $config->bundle = $composer['autoload']['psr-4'];
        } else {
            if ($displayTitle) {
                $io->title('Bundle configuration');
            }
            $psr = explode('=>', $io->ask('psr-4 autoload mapping?', 'App\ => src/', $notBlank));
            $config->bundle = [
                trim($psr[0]) => trim($psr[1]),
            ];
        }
    }
}
