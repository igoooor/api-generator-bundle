<?php
/**
 * Created by PhpStorm.
 * User: igorweigel
 * Date: 07.10.2020
 * Time: 15:32
 */

namespace Igoooor\ApiGeneratorBundle\Generator;

use Igoooor\ApiGeneratorBundle\Generator\Configuration\Configuration;

/**
 * Class CrudGenerator
 */
class CrudGenerator extends CrudEventGenerator
{
    /**
     * @var CrudEventGenerator
     */
    private CrudEventGenerator $crudEventGenerator;

    /**
     * CrudGenerator constructor.
     *
     * @param CrudEventGenerator $crudEventGenerator
     */
    public function __construct(CrudEventGenerator $crudEventGenerator)
    {
        $this->crudEventGenerator = $crudEventGenerator;
    }

    /**
     * @param Configuration $config
     */
    public function generate(Configuration $config): void
    {
        $this->renderFile(
            'controller/Controller.php.twig',
            sprintf('%s/%s/%sController.php', $config->projectDir, $config->controllerPath, $config->entityName),
            $config->toArray()
        );
        if ($config->methods['new']) {
            $this->renderFile(
                'form/FormType.php.twig',
                sprintf('%s/%sForm/Type/%sType.php', $config->projectDir, reset($config->bundle), $config->entityName),
                $config->toArray()
            );
        }
        foreach ($config->crudEvents as $crudEvent => $enabled) {
            if (!$enabled) {
                continue;
            }
            $this->generateEvent($config, $crudEvent);
        }
    }
}
