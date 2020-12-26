<?php declare(strict_types=1);

namespace Pollen\Social;

use InvalidArgumentException, RuntimeException;
use Pollen\Social\Adapters\AdapterInterface;
use Pollen\Social\Channels\DailymotionChannel;
use Pollen\Social\Channels\FacebookChannel;
use Pollen\Social\Channels\GooglePlusChannel;
use Pollen\Social\Channels\InstagramChannel;
use Pollen\Social\Channels\LinkedinChannel;
use Pollen\Social\Channels\PinterestChannel;
use Pollen\Social\Channels\SocialChannelDriver;
use Pollen\Social\Channels\SocialChannelDriverInterface;
use Pollen\Social\Channels\TwitterChannel;
use Pollen\Social\Channels\ViadeoChannel;
use Pollen\Social\Channels\VimeoChannel;
use Pollen\Social\Channels\YoutubeChannel;
use Pollen\Social\Contracts\SocialContract;
use Pollen\Social\Partial\SocialMenuPartial;
use Pollen\Social\Partial\SocialSharePartial;
use Psr\Container\ContainerInterface as Container;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Partial\Partial as PartialManagerContract;
use tiFy\Contracts\View\Engine as ViewEngine;
use tiFy\Partial\Partial as PartialManager;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ContainerAwareTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Storage;
use tiFy\Support\Proxy\View;

class Social implements SocialContract
{
    use BootableTrait;
    use ContainerAwareTrait;

    /**
     * Instance de la classe.
     * @var static|null
     */
    private static $instance;

    /**
     * Instance de l'adapteur associé.
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Instance du gestionnaire de configuration.
     * @var ParamsBag
     */
    protected $configBag;

    /**
     * Instances de pilotes de réseaux chargés.
     * @var SocialChannelDriverInterface[]|array
     */
    private $channels = [];

    /**
     * Déclaration de réseaux à charger.
     * @var SocialChannelDriverInterface[]|string[]|array
     */
    private $channelDefinitions = [];

    /**
     * Liste des réseaux disponibles.
     * @var string[]
     */
    private $defaultsChannels = [
        'dailymotion' => DailymotionChannel::class,
        'facebook'    => FacebookChannel::class,
        'google-plus' => GooglePlusChannel::class,
        'instagram'   => InstagramChannel::class,
        'linkedin'    => LinkedinChannel::class,
        'pinterest'   => PinterestChannel::class,
        'twitter'     => TwitterChannel::class,
        'viadeo'      => ViadeoChannel::class,
        'vimeo'       => VimeoChannel::class,
        'youtube'     => YoutubeChannel::class,
    ];

    /**
     * Liste des services par défaut fournis par conteneur d'injection de dépendances.
     * @var array
     */
    private $defaultProviders = [];

    /**
     * Instance du gestionnaire des ressources
     * @var LocalFilesystem|null
     */
    private $resources;

    /**
     * Moteur des gabarits d'affichage.
     * @var ViewEngine
     */
    protected $viewEngine;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], Container $container = null)
    {
        $this->setConfig($config);

        if (!is_null($container)) {
            $this->setContainer($container);
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * @inheritDoc
     */
    public static function instance(): SocialContract
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new RuntimeException(sprintf('Unavailable %s instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): SocialContract
    {
        if (!$this->isBooted()) {
            events()->trigger('social.booting', [$this]);

            $registered = [];
            if ($channels = $this->config('channel', [])) {
                foreach ($channels as $k => $v) {
                    $registered[] = is_numeric($k) ? $v : $k;
                }
                $registered = array_intersect($registered, array_keys($this->defaultsChannels));
            } else {
                $registered = array_keys($this->defaultsChannels);
            }

            if ($registered) {
                foreach ($registered as $name) {
                    $this->registerChannel($name, array_merge([
                        'driver' => $this->defaultsChannels[$name],
                    ], $channels[$name] ?? []));
                }
            }

            $partialManager = ($this->containerHas(PartialManagerContract::class))
                ? $this->containerGet(PartialManagerContract::class) : new PartialManager();

            $partialManager->register('social-menu', $this->containerHas(SocialMenuPartial::class)
                ? SocialMenuPartial::class : new SocialMenuPartial($this, $partialManager));
            $partialManager->register('social-share', $this->containerHas(SocialSharePartial::class)
                ? SocialSharePartial::class : new SocialSharePartial($this, $partialManager));

            $this->setBooted();

            events()->trigger('social.booted', [$this]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function config($key = null, $default = null)
    {
        if (!isset($this->configBag) || is_null($this->configBag)) {
            $this->configBag = new ParamsBag();
        }

        if (is_string($key)) {
            return $this->configBag->get($key, $default);
        } elseif (is_array($key)) {
            return $this->configBag->set($key);
        } else {
            return $this->configBag;
        }
    }

    /**
     * @inheritDoc
     */
    public function getAdapter(): ?AdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function getChannel(string $name): ?SocialChannelDriverInterface
    {
        return $this->loadChannel($name);
    }

    /**
     * @inheritDoc
     */
    public function getChannelLink(string $name, array $attrs = []): string
    {
        return ($channel = $this->getChannel($name)) ? $channel->pageLink($attrs) : '';
    }

    /**
     * @inheritDoc
     */
    public function getChannels(): array
    {
        return $this->loadChannels()->channels;
    }

    /**
     * @inheritDoc
     */
    public function getProvider(string $name)
    {
        return $this->config("providers.{$name}", $this->defaultProviders[$name] ?? null);
    }

    /**
     * @inheritDoc
     */
    public function loadChannel(string $name): ?SocialChannelDriverInterface
    {
        if (isset($this->channels[$name])) {
            return $this->channels[$name];
        } elseif (!$def = $this->channelDefinitions[$name] ?? null) {
            throw new InvalidArgumentException(sprintf('SocialChannel [%s] not registered.', $name));
        }

        if (is_array($def)) {
            $driver = $def['driver'] ?? null;
            $params = $def;
            unset($def['driver']);
        } else {
            $driver = $def;
            $params = [];
        }

        if (!$driver) {
            $driver = SocialChannelDriver::class;
        }

        if (is_object($driver)) {
            $channel = $driver;
        } elseif (class_exists($driver)) {
            $channel = new $driver($this);
        } elseif (is_string($driver) && $this->containerHas($driver)) {
            $channel = $this->containerGet($driver);
        } else {
            $channel = new SocialChannelDriver($this);
        }

        if ($driver instanceof SocialChannelDriverInterface) {
            throw new InvalidArgumentException(sprintf('Unable to boot SocialChannel [%s] .', $name));
        } else {
            if (!$channel->getName()) {
                $channel->setName($name);
            }

            return $this->channels[$name] = $channel->setParams($params)->boot();
        }
    }

    /**
     * @inheritDoc
     */
    public function loadChannels(): SocialContract
    {
        foreach (array_keys($this->channelDefinitions) as $name) {
            $this->loadChannel($name);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function registerChannel(string $name, $channelDefinition): SocialContract
    {
        unset($this->channels[$name]);
        $this->channelDefinitions[$name] = $channelDefinition;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resources(?string $path = null)
    {
        if (!isset($this->resources) ||is_null($this->resources)) {
            $this->resources = Storage::local(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources');
        }

        return is_null($path) ? $this->resources : $this->resources->path($path);
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(AdapterInterface $adapter): SocialContract
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setConfig(array $attrs): SocialContract
    {
        $this->config($attrs);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $name = null, array $data = [])
    {
        if (is_null($this->viewEngine)) {
            $this->viewEngine = $this->containerHas('social.view-engine')
                ? $this->containerGet('social.view-engine') : View::getPlatesEngine();
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($name, $data);
    }
}
