<?php

namespace Symfony\Tests\Component\Security\Core\User;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Symfony\Component\Security\Core\User\ChainUserProvider;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class ChainUserProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadUserByUsername()
    {
        $provider1 = $this->getProvider();
        $provider1
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($this->equalTo('foo'))
            ->will($this->throwException(new UsernameNotFoundException('not found')))
        ;

        $provider2 = $this->getProvider();
        $provider2
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue($account = $this->getAccount()))
        ;

        $provider = new ChainUserProvider(array($provider1, $provider2));
        $this->assertSame($account, $provider->loadUserByUsername('foo'));
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UsernameNotFoundException
     */
    public function testLoadUserByUsernameThrowsUsernameNotFoundException()
    {
        $provider1 = $this->getProvider();
        $provider1
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($this->equalTo('foo'))
            ->will($this->throwException(new UsernameNotFoundException('not found')))
        ;

        $provider2 = $this->getProvider();
        $provider2
            ->expects($this->once())
            ->method('loadUserByUsername')
            ->with($this->equalTo('foo'))
            ->will($this->throwException(new UsernameNotFoundException('not found')))
        ;

        $provider = new ChainUserProvider(array($provider1, $provider2));
        $provider->loadUserByUsername('foo');
    }

    public function testloadUser()
    {
        $provider1 = $this->getProvider();
        $provider1
            ->expects($this->once())
            ->method('loadUser')
            ->will($this->throwException(new UnsupportedUserException('unsupported')))
        ;

        $provider2 = $this->getProvider();
        $provider2
            ->expects($this->once())
            ->method('loadUser')
            ->will($this->returnValue($account = $this->getAccount()))
        ;

        $provider = new ChainUserProvider(array($provider1, $provider2));
        $this->assertSame($account, $provider->loadUser($this->getAccount()));
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\UnsupportedUserException
     */
    public function testloadUserThrowsUnsupportedUserException()
    {
        $provider1 = $this->getProvider();
        $provider1
            ->expects($this->once())
            ->method('loadUser')
            ->will($this->throwException(new UnsupportedUserException('unsupported')))
        ;

        $provider2 = $this->getProvider();
        $provider2
            ->expects($this->once())
            ->method('loadUser')
            ->will($this->throwException(new UnsupportedUserException('unsupported')))
        ;

        $provider = new ChainUserProvider(array($provider1, $provider2));
        $provider->loadUser($this->getAccount());
    }

    public function testSupportsClass()
    {
        $provider1 = $this->getProvider();
        $provider1
            ->expects($this->once())
            ->method('supportsClass')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue(false))
        ;

        $provider2 = $this->getProvider();
        $provider2
            ->expects($this->once())
            ->method('supportsClass')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue(true))
        ;

        $provider = new ChainUserProvider(array($provider1, $provider2));
        $this->assertTrue($provider->supportsClass('foo'));
    }

    public function testSupportsClassWhenNotSupported()
    {
        $provider1 = $this->getProvider();
        $provider1
            ->expects($this->once())
            ->method('supportsClass')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue(false))
        ;

        $provider2 = $this->getProvider();
        $provider2
            ->expects($this->once())
            ->method('supportsClass')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue(false))
        ;

        $provider = new ChainUserProvider(array($provider1, $provider2));
        $this->assertFalse($provider->supportsClass('foo'));
    }

    protected function getAccount()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserInterface');
    }

    protected function getProvider()
    {
        return $this->getMock('Symfony\Component\Security\Core\User\UserProviderInterface');
    }
}