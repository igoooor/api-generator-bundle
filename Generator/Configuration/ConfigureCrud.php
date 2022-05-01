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
 * Class ConfigureCrud
 */
class ConfigureCrud
{
    /**
     * @param SymfonyStyle  $io
     * @param Configuration $config
     */
    public function configure(SymfonyStyle $io, Configuration $config)
    {
        $io->title('CRUD configuration');
        $io->section('Enabled methods');
        if (!$io->confirm('Enable all methods', true)) {
            foreach ($config->methods as $method => $enabled) {
                $config->methods[$method] = $io->confirm($method, $enabled);
            }
        }

        $io->section('Authorization');
        $config->defaultRole = $this->askRole($io, 'Default Role?', $config->defaultRole);
        $config->roles = [
            'list'   => $config->defaultRole,
            'new'    => $config->defaultRole,
            'detail' => $config->defaultRole,
            'update' => $config->defaultRole,
            'delete' => $config->defaultRole,
        ];
        if (!$io->confirm('All methods are using the default role?', true)) {
            foreach ($config->roles as $method => $role) {
                if (!$config->methods[$method]) {
                    continue;
                }

                $config->roles[$method] = $this->askRole($io, $method, $config->defaultRole);
            }
        }

        $io->section('Override CRUD Controller methods');
        if ($io->confirm('Do you need to override some methods?', false)) {
            foreach ($config->overrides as $override => $value) {
                $config->overrides[$override] = $io->confirm($override, $config->overrides[$override]);
            }
        }

        ConfigureCrudEvent::configure($io, $config);
    }

    private function askRole(SymfonyStyle $io, string $question, string $defaultValue): string
    {
        $notBlank = function ($val) {
            if (empty($val)) {
                throw new \RuntimeException('Can not be blank.');
            }

            return $val;
        };

        $roles = ['IS_AUTHENTICATED_ANONYMOUSLY', 'ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
        if (!in_array($defaultValue, $roles)) {
            $roles[] = $defaultValue;
        }
        $roles[] = 'other';

        $role = $io->choice($question, $roles, $defaultValue);
        if ('other' === $role) {
            $role = $io->ask($question, '', $notBlank);
        }

        return $role;
    }
}
