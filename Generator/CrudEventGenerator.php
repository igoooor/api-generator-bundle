<?php
/**
 * Created by PhpStorm.
 * User: igorweigel
 * Date: 07.10.2020
 * Time: 15:32
 */

namespace Igoooor\ApiGeneratorBundle\Generator;

use Igoooor\ApiGeneratorBundle\Generator\Configuration\Configuration;
use ReflectionClass;

/**
 * Class CrudEventGenerator
 */
class CrudEventGenerator extends Generator
{
    /**
     * @param Configuration $config
     *
     * @throws \ReflectionException
     */
    public function generate(Configuration $config): void
    {
        $controllerFqcn = sprintf('%s\%sController', $config->controllerNamespace, $config->entityName);
        $reflectionClass = new ReflectionClass($controllerFqcn);
        $configureCrud = $reflectionClass->getMethod('configureCrud');
        $length = $configureCrud->getEndLine() - $configureCrud->getStartLine();
        $source = file($reflectionClass->getFileName());
        $methodBody = array_slice($source, $configureCrud->getStartLine(), $length);
        $originalMethodBody = implode('', $methodBody);
        $newLine = null;
        $insertAt = null;
        for ($i = count($methodBody) - 1; $i >= 0; $i--) {
            $line = $methodBody[$i];
            if (0 !== preg_match('/^(\s*);/', $line, $matches)) {
                $newLine = sprintf("%s%%s\n", $matches[1]);
                $insertAt = $i;
                break;
            } elseif (0 !== preg_match('/^(\s*)->(.*);/', $line, $matches)) {
                $newLine = sprintf("%s%%s\n", $matches[1]);
                $methodBody[$i] = sprintf("%s->%s\n", $matches[1], $matches[2]);
                $endLine = sprintf("%s;\n", $matches[1]);
                array_splice($methodBody, $i + 1, 0, [$endLine]);
                $insertAt = $i + 1;
                break;
            }
        }

        foreach ($config->crudEvents as $crudEvent => $enabled) {
            if (!$enabled) {
                continue;
            }

            $this->generateEvent($config, $crudEvent);
            if (null === $insertAt) {
                continue;
            }

            $controllerLine = sprintf('->set%sFqcn(%s%s::class)', $crudEvent, $config->entityName, $crudEvent);
            $controllerLine = sprintf($newLine, $controllerLine);
            array_splice($methodBody, $insertAt, 0, [$controllerLine]);

            $useInsertAt = array_key_first(preg_grep('/^use\s.*;/', $source));
            $useEventLine = sprintf(
                "use %sEvent\%s\%s%s;\n",
                array_key_first($config->bundle),
                $config->entityName,
                $config->entityName,
                $crudEvent,
            );
            array_splice($source, $useInsertAt, 0, [$useEventLine]);
        }

        if (null === $insertAt) {
            self::writeln(sprintf('  <fg=red>skipped</> %s', $reflectionClass->getFileName()));
        } else {
            $newSource = str_replace($originalMethodBody, implode('', $methodBody), implode('', $source));
            self::dump($reflectionClass->getFileName(), $newSource);
        }
    }

    /**
     * @param Configuration $config
     * @param string        $crudEvent
     */
    protected function generateEvent(Configuration $config, string $crudEvent): void
    {
        $eventConfig = clone $config;
        $eventConfig->eventType = $crudEvent;
        $eventConfig->eventName = $config->entityName.$crudEvent;
        $eventConfig->entityNameCamelcase = lcfirst($config->entityName);
        $this->renderFile(
            'crudEvent/Event.php.twig',
            sprintf('%s/%sEvent/%s/%s.php', $config->projectDir, reset($config->bundle), $config->entityName, $eventConfig->eventName),
            $eventConfig->toArray()
        );
        $eventConfig->listenerType = str_replace('Event', '', $crudEvent);
        $eventConfig->listenerName = $config->entityName.$eventConfig->listenerType;
        $this->renderFile(
            'crudListener/Listener.php.twig',
            sprintf('%s/%sListener/%s/%sSubscriber.php', $config->projectDir, reset($config->bundle), $config->entityName, $eventConfig->listenerName),
            $eventConfig->toArray()
        );
    }
}
