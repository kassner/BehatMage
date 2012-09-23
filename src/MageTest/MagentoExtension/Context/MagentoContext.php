<?php

namespace MageTest\MagentoExtension\Context;

use Mage_Core_Model_App as MageApp;
use MageTest\MagentoExtension\Context\MagentoAwareContext,
    MageTest\MagentoExtension\Service\ConfigManager,
    MageTest\MagentoExtension\Service\CacheManager,
    MageTest\MagentoExtension\Service,
    MageTest\MagentoExtension\Fixture\FixtureFactory,
    MageTest\MagentoExtension\Service\Session;

use Behat\MinkExtension\Context\MinkAwareInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Mink\Mink;

class MagentoContext extends BehatContext
    implements MinkAwareInterface, MagentoAwareInterface
{
    private $app;
    private $configManager;
    private $cacheManager;
    private $factory;
    private $mink;
    private $minkProperties;
    private $sessionService;

    /**
     * @Given /^I log in as admin$/
     */
    public function iLoginAsAdmin()
    {
        $sid = $this->sessionService->adminLogin('admin', '123123pass');
        $this->mink->getSession()->setCookie('adminhtml', $sid);
    }

    /**
     * @When /^I open admin URI "([^"]*)"$/
     */
    public function iOpenAdminUri($uri)
    {
        $urlModel = new \Mage_Adminhtml_Model_Url();
        if (preg_match('@^/admin/(.*?)/(.*?)((/.*)?)$@', $uri, $m)) {
            $processedUri = "/admin/{$m[1]}/{$m[2]}/key/".$urlModel->getSecretKey($m[1], $m[2])."/{$m[3]}";
            $this->mink->getSession()->visit($processedUri);
        } else {
            throw new \InvalidArgumentException('$uri parameter should start with /admin/ and contain controller and action elements');
        }
    }

    /**
     * @Then /^I should see text "([^"]*)"$/
     */
    public function iShouldSeeText($text)
    {
        $select = '//*[text()="'.$text.'"]';
        if (!$this->mink->getSession()->getDriver()->find($select)) {
            throw new \Behat\Mink\Exception\ElementNotFoundException($this->mink->getSession(), 'xpath', $select, null);
        }
    }

    public function setApp(MageApp $app)
    {
        $this->app = $app;
    }

    public function setConfigManager(ConfigManager $config)
    {
        $this->configManager = $config;
    }

    public function setCacheManager(CacheManager $cache)
    {
        $this->cacheManager = $cache;
    }

    public function setFixtureFactory(FixtureFactory $factory)
    {
        $this->factory = $factory;
    }

    public function setSessionService(Session $session)
    {
        $this->sessionService = $session;
    }

    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    public function getFixture($identifier)
    {
        return $this->factory->create($identifier);
    }
}
}
