<?php
/**
 * Created by PhpStorm.
 * User: igorweigel
 * Date: 07.10.2020
 * Time: 22:11
 */

namespace Igoooor\ApiGeneratorBundle\Generator\Configuration;

use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ConfigureEntity
 */
class ConfigureEntity
{
    /**
     * @param SymfonyStyle  $io
     * @param Configuration $config
     * @param bool          $full
     *
     * @throws \ReflectionException
     */
    public function configure(SymfonyStyle $io, Configuration $config, bool $full = true)
    {
        $notBlank = function ($val) {
            if (empty($val)) {
                throw new \RuntimeException('Can not be blank.');
            }

            return $val;
        };

        $io->title('Entity configuration');
        $config->entityName = $io->ask('Name?', '', $notBlank);
        $config->entityNamespace = $io->ask('Namespace?', array_key_first($config->bundle).'Entity', $notBlank);
        $config->entityFqcn = sprintf('%s\%s', $config->entityNamespace, $config->entityName);
        if ($full) {
            $config->softDelete = $io->confirm('Softdelete?', false);
        }

        $reader = new AnnotationReader();
        $reflectionClass = new ReflectionClass($config->entityNamespace.'\\'.$config->entityName);
        /** @var \Doctrine\ORM\Mapping\Entity $entityAnnotation */
        $entityAnnotation = $reader->getClassAnnotation($reflectionClass, \Doctrine\ORM\Mapping\Entity::class);
        $config->repositoryFqcn = $entityAnnotation->repositoryClass;
    }
}
