<?php
/**
 * Created by PhpStorm.
 * User: igorweigel
 * Date: 07.10.2020
 * Time: 15:37
 */

namespace Igoooor\ApiGeneratorBundle\Generator;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Class Generator
 */
class Generator
{
    /**
     * @var OutputInterface
     */
    protected static ?OutputInterface $output = null;

    /**
     * Sets an array of directories to look for templates.
     *
     * The directories must be sorted from the most specific to the most
     * directory.
     *
     * @param array $skeletonDirs An array of skeleton dirs
     */
    public function setSkeletonDirs($skeletonDirs)
    {
        $this->skeletonDirs = is_array($skeletonDirs) ? $skeletonDirs : array($skeletonDirs);
    }

    /**
     * Gets the twig environment that will render skeletons.
     *
     * @return \Twig_Environment
     */
    protected function getTwigEnvironment()
    {
        return new Environment(new FilesystemLoader(__DIR__.'/../Resources/skeleton'), array(
            'debug' => true,
            'cache' => false,
            'strict_variables' => true,
            'autoescape' => false,
        ));
    }

    protected function render($template, $parameters)
    {
        return $this->getTwigEnvironment()->render($template, $parameters);
    }

    protected function renderFile($template, $target, $parameters)
    {
        self::mkdir(dirname($target));

        return self::dump($target, $this->render($template, $parameters));
    }

    /**
     * @internal
     */
    public static function mkdir($dir, $mode = 0777, $recursive = true)
    {
        if (!is_dir($dir)) {
            mkdir($dir, $mode, $recursive);
            self::writeln(sprintf('  <fg=green>created</> %s', self::relativizePath($dir)));
        }
    }

    /**
     * @internal
     */
    public static function dump($filename, $content)
    {
        if (file_exists($filename)) {
            self::writeln(sprintf('  <fg=yellow>updated</> %s', self::relativizePath($filename)));
        } else {
            self::writeln(sprintf('  <fg=green>created</> %s', self::relativizePath($filename)));
        }

        return file_put_contents($filename, $content);
    }

    protected static function writeln($message)
    {
        if (null === self::$output) {
            self::$output = new ConsoleOutput();
        }

        self::$output->writeln($message);
    }

    protected static function relativizePath($absolutePath)
    {
        $relativePath = str_replace(getcwd(), '.', $absolutePath);

        return is_dir($absolutePath) ? rtrim($relativePath, '/').'/' : $relativePath;
    }
}
