<?php
namespace Offworks\Hex\Commands;

use Offworks\Hex\Contracts\ProviderInterface;
use Offworks\Hex\Providers\DefaultProvider;
use Offworks\Hex\Providers\ProviderCollection;
use Offworks\Wizard\Arguments;
use Offworks\Wizard\Command;
use Offworks\Wizard\Options;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class StartCommand extends Command
{
    const TYPE_CUSTOM = 'custom';
    const TYPE_PRESET = 'preset';

    protected $providers;

    public function __construct(ProviderCollection $providers = null)
    {
        $this->providers = !$providers ? ProviderCollection::createDefault($this) : $providers;

        parent::__construct();
    }

    /**
     * @return ProviderCollection
     */
    public function getProviders()
    {
        return $this->providers;
    }

    /**
     * Configure the console details
     * Alias to configure()
     * @return mixed
     */
    public function setup()
    {
        $this->setName('hex:start');
    }

    /**
     * Handle the command execution
     * For more information visit http://symfony.com/doc/current/console.html
     * @param Arguments $arguments
     * @param Options $options
     * @return
     */
    public function handle(Arguments $arguments, Options $options)
    {
        if(file_exists('composer.json')) {
            if($this->ask(new ConfirmationQuestion('Existing [composer.json] already exist. Continue? (y/n) :')) != 'y') {
                return;
            }
        }

        $providers = array();

        foreach($provs = $this->providers->getAll() as $provider)
            $providers[] = $provider->getName();

        $provider = $provs[$this->simplyChoose('Select provider : ', $providers, 1)];

        $components = $provider->getComponents();

        $comps = array();
        foreach($components as $component) {
            $comps[$component] = true;
        }

        $comps = $this->configureArray('Configure components you needed.', null, $comps);

        $packages = array();

        // now to select first option.

        foreach($comps as $component => $enabled) {
            $options = $provider->getPackages($component);

            if(!$options)
                $packages[$component] = false;

            foreach($options as $package) {
                break;
            }

            $packages[$component] = $package;
        }

        $packages = $this->configureComponents($packages, $provider);

        $composer = array('require' => array());

        foreach($packages as $component => $package) {
            $composer['require'][$package['name']] = $package['version'];
        }

        file_put_contents('composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        if(!$this->ask(new ConfirmationQuestion('Do a composer update?'), 'n'))
            return;

        shell_exec('composer update');
    }

    protected function configureComponents(array $components, ProviderInterface $provider)
    {
        $packages = array();

        foreach($components as $component => $package) {
            $name = $package['name'];
            $version = $package['version'];

            $packages[$component] = '<info>' . $component . '</info> ' . $name . ' ' . $version;
        }

        $packages['save'] = array(
            'description' => 'save',
            'option' => 'y'
        );

        $choice = $this->simplyChoose('List of components to be installed', $packages, 1);

        if($choice == 'save')
            return $components;

        $options = array();

        foreach($provider->getPackages($choice) as $package) {
            $options[] = $package['name'];
        }

        $index = $this->simplyChoose('Select package for ' . $choice, $options, 1);

        $package = $provider->getPackages($choice)[$index];

        $components[$choice] = $package;

        return $this->configureComponents($components, $provider);
    }
}