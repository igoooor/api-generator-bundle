<?php
/**
 * Created by PhpStorm.
 * User: igorweigel
 * Date: 07.10.2020
 * Time: 21:04
 */

namespace Igoooor\ApiGeneratorBundle\Generator\Configuration;

/**
 * Class Configuration
 */
class Configuration
{
    public string $projectDir = '';
    public string $composerFile = '';
    public array $bundle = [];
    public string $entityFqcn = '';
    public string $repositoryFqcn = '';
    public string $entityName = '';
    public string $entityNamespace = '';
    public bool $softDelete = false;
    public string $controllerPath = '';
    public string $controllerFqcn = '';
    public string $controllerName = '';
    public string $controllerNamespace = '';
    public string $routePath = '';
    public string $routeName = '';
    public array $methods = [
        'list' => true,
        'new' => true,
        'detail' => true,
        'update' => true,
        'delete' => true,
    ];
    public $defaultRole = 'IS_AUTHENTICATED_ANONYMOUSLY';
    public array $roles = [
        'list' => 'IS_AUTHENTICATED_ANONYMOUSLY',
        'new' => 'IS_AUTHENTICATED_ANONYMOUSLY',
        'detail' => 'IS_AUTHENTICATED_ANONYMOUSLY',
        'update' => 'IS_AUTHENTICATED_ANONYMOUSLY',
        'delete' => 'IS_AUTHENTICATED_ANONYMOUSLY',
    ];
    public array $overrides = [
        'findAll' => false,
        'getSerializerGroups' => false,
        'getEntity' => false,
        'createEntity' => false,
        'persistEntity' => false,
        'updateEntity' => false,
        'deleteEntity' => false,
    ];
    public array $crudEvents = [
        'PreCreatedEvent' => false,
        'PostCreatedEvent' => false,
        'PreUpdatedEvent' => false,
        'PostUpdatedEvent' => false,
        'PreDeletedEvent' => false,
        'PostDeletedEvent' => false,
    ];
    public string $eventType = '';
    public string $eventName = '';
    public string $entityNameCamelcase = '';
    public string $listenerType = '';
    public string $listenerName = '';

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'bundleName' => array_key_first($this->bundle),
            'entityFqcn' => $this->entityFqcn,
            'repositoryFqcn' => $this->repositoryFqcn,
            'entityName' => $this->entityName,
            'entityNamespace' => $this->entityNamespace,
            'softDelete' => $this->softDelete,
            'controllerPath' => $this->controllerPath,
            'controllerNamespace' => $this->controllerNamespace,
            'controllerName' => $this->controllerName,
            'routePath' => $this->routePath,
            'routeName' => $this->routeName,
            'methods' => $this->methods,
            'defaultRole' => $this->defaultRole,
            'roles' => $this->roles,
            'overrides' => $this->overrides,
            'crudEvents' => $this->crudEvents,
            'eventType' => $this->eventType,
            'eventName' => $this->eventName,
            'entityNameCamelcase' => $this->entityNameCamelcase,
            'listenerType' => $this->listenerType,
            'listenerName' => $this->listenerName,
        ];
    }
}
