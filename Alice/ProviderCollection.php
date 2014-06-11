<?php

namespace Knp\RadBundle\Alice;

class ProviderCollection
{
    protected $providers;

    public function __construct()
    {
        $this->providers = array();
    }

    public function addProvider($provider)
    {
        if (!in_array($provider, $this->providers)) {
            $this->providers[] = $provider;
        }

        return $this;
    }

    public function getProviders()
    {
        return $this->providers;
    }
}
