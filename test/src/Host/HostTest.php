<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Deployer\Host;

use PHPUnit\Framework\TestCase;

class HostTest extends TestCase
{
    public function testHost()
    {
        $host = new Host('host');
        $host
            ->hostname('host')
            ->user('user')
            ->port(22)
            ->configFile('~/.ssh/config')
            ->identityFile('~/.ssh/id_rsa')
            ->forwardAgent(true)
            ->multiplexing(true)
            ->options(['BatchMode=yes'])
            ->addOption('Compression=yes');

        self::assertEquals('host', $host->getHostname());
        self::assertEquals('user', $host->getUser());
        self::assertEquals(22, $host->getPort());
        self::assertEquals('~/.ssh/config', $host->getConfigFile());
        self::assertEquals('~/.ssh/id_rsa', $host->getIdentityFile());
        self::assertEquals(true, $host->isForwardAgent());
        self::assertEquals(true, $host->isMultiplexing());
        self::assertEquals(['BatchMode=yes', 'Compression=yes'], $host->getOptions());
        self::assertContains(' -p 22 -F ~/.ssh/config -i ~/.ssh/id_rsa -A -o BatchMode=yes -o Compression=yes', $host->sshOptions());
        self::assertEquals('user@host', "$host");
    }

    public function testHostWithCustomPort()
    {
        $host = new Host('host');
        $host
            ->hostname('host')
            ->user('user')
            ->port(2222);

        self::assertEquals(' -p 2222 -A', $host->sshOptions());
        self::assertEquals('user@host', "$host");
    }

    public function testConfigurationAccessor()
    {
        $host = new Host('host');
        $host
            ->stage('stage')
            ->roles('db', 'app')
            ->set('key', 'value')
            ->set('array', [1])
            ->add('array', [2]);

        self::assertEquals('stage', $host->get('stage'));
        self::assertEquals(['db', 'app'], $host->get('roles'));
        self::assertEquals('value', $host->get('key'));
        self::assertEquals([1, 2], $host->get('array'));
    }

    public function testHostAlias()
    {
        $host = new Host('host/alias');
        self::assertEquals('host/alias', $host->getHostname());
        self::assertEquals('host', "$host");
    }
}
