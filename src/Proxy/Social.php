<?php declare(strict_types=1);

namespace Pollen\Social\Proxy;

use tiFy\Support\Proxy\AbstractProxy;
use Pollen\Social\Channels\SocialChannelDriverInterface;
use Pollen\Social\Contracts\SocialContract;

/**
 * @method static SocialChannelDriverInterface addChannel(string $name, SocialChannelDriverInterface|array|string $attrs)
 * @method static SocialChannelDriverInterface|null getChannel(string $name)
 * @method static string getChannelLink(string $name, array $attrs = [])
 * @method static SocialChannelDriverInterface[]|array getChannels()
 * @method static SocialContract setConfig(array $config)
 */
class Social extends AbstractProxy
{
    /**
     * {@inheritDoc}
     *
     * @return SocialContract|object
     */
    public static function getInstance()
    {
        return parent::getInstance();
    }

    /**
     * @inheritDoc
     */
    public static function getInstanceIdentifier(): string
    {
        return SocialContract::class;
    }
}
