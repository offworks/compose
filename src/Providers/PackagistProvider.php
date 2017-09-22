<?php
namespace Offworks\Hex\Providers;

use Offworks\Hex\Contracts\ProviderInterface;

class PackagistProvider implements ProviderInterface
{
    /**
     * @var array
     */
    protected $cache = array();

    /**
     * Provider name
     *
     * @return string
     */
    public function getName()
    {
        return 'Packagist';
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

        foreach($this->getPackagesFromPackagist($component) as $package) {
            $packages[] = array(
                'name' => $package,
                'version' => '*'
            );
        }

        return $packages;
    }

    protected function getPackagesFromPackagist($type)
    {
        if(isset($this->cache[$type]))
            return $this->cache[$type];

        return $this->cache[$type] = json_decode(file_get_contents('https://packagist.org/packages/list.json?type=' . $type), true)['packageNames'];
    }

    /**
     * Get all components
     *
     * @return array
     */
    public function getComponents()
    {
        return array('microframework');
    }
}