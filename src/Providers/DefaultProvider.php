<?php
namespace Offworks\Hex\Providers;

use Offworks\Hex\Contracts\ProviderInterface;

class DefaultProvider implements ProviderInterface
{
    protected $root;

    protected $repo;

    public function __construct($root)
    {
        $this->root = $root;

        $this->repo = json_decode(file_get_contents($root . '/repo/default/packages.json'), true);
    }

    public function getName()
    {
        return 'Default';
    }

    public function getPackages($type)
    {
        return isset($this->repo[$type]) ? $this->repo[$type] : array();
    }

    /**
     * Get all components
     *
     * @return array
     */
    public function getComponents()
    {
        return array_keys($this->repo);
    }
}