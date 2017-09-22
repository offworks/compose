<?php
namespace Offworks\Hex\Contracts;

interface ProviderInterface
{
    /**
     * Provider name
     *
     * @return string
     */
    public function getName();

    /**
     * Get all components
     *
     * @return array
     */
    public function getComponents();

    /**
     * Get package
     *
     * @param string $component
     * @return array
     */
    public function getPackages($component);
}