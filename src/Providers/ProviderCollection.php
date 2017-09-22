<?php
namespace Offworks\Hex\Providers;

use Offworks\Hex\Commands\StartCommand;
use Offworks\Hex\Contracts\ProviderInterface;

class ProviderCollection
{
    /**
     * @var ProviderInterface[]
     */
    protected $providers;

    public function __construct(array $providers = array())
    {
        foreach($providers as $provider) {
            $this->add($provider);
        }
    }

    public static function createDefault(StartCommand $command)
    {
        return new static(array(
            new DefaultProvider(__DIR__ . '/../..'),
            new PackagistProvider(),
            new SearchProvider($command)
        ));
    }

    public function add(ProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }

    public function getAll()
    {
        return $this->providers;
    }
}