<?php
namespace Offworks\Hex\Providers;

use Offworks\Hex\Commands\StartCommand;
use Offworks\Hex\Contracts\ProviderInterface;

class SearchProvider implements ProviderInterface
{
    /**
     * @var StartCommand
     */
    protected $command;

    public function __construct(StartCommand $command)
    {
        $this->command = $command;
    }

    /**
     * Provider name
     *
     * @return string
     */
    public function getName()
    {
        return 'Search';
    }

    /**
     * Get all components
     *
     * @return array
     */
    public function getComponents()
    {
        $components = explode(',', $this->command->ask('Specify the components you need (comma seperated) : '));

        return $components;
    }

    /**
     * Get package
     *
     * @param string $component
     * @return array
     */
    public function getPackages($component)
    {
        $packages = array();

        $providers = $this->command->getProviders();

        foreach($providers->getAll() as $provider) {
            if($provider instanceof SearchProvider)
                continue;

            foreach($provider->getPackages($component) as $package)
                $packages[] = $package;
        }

        return $packages;
    }
}