<?php
declare(strict_types=1);

namespace Lsr\CQRS\DI;

use Lsr\CQRS\AsyncCommandBusInterface;
use Lsr\CQRS\CommandBus;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

/**
 * @property-read object{asyncBus: null|string|AsyncCommandBusInterface}&stdClass $config
 */
class CqrsExtension extends CompilerExtension
{

    public function getConfigSchema() : Schema {
        return Expect::structure(
          [
            'asyncBus' => Expect::anyOf(
              Expect::null(),
              Expect::string(),
              Expect::type(AsyncCommandBusInterface::class)
            )->default(null),
          ]
        );
    }

    public function loadConfiguration() : void {
        $builder = $this->getContainerBuilder();

        $asyncBus = $this->config->asyncBus;
        if (is_string($asyncBus)) {
            $asyncBus = '@'.$asyncBus;
        }
        $builder->addDefinition($this->prefix('command.bus'))
                ->setType(CommandBus::class)
                ->setFactory(
                  CommandBus::class,
                  [
                    '@app',
                    $asyncBus,
                  ],
                )
                ->setTags(['lsr', 'cqrs']);
    }

}