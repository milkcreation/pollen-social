<?php
declare(strict_types=1);

namespace Pollen\Social\Channels;

use Exception;
use BadMethodCallException;
use Detection\MobileDetect;
use tiFy\Contracts\View\Engine as ViewEngine;
use Pollen\Social\Contracts\SocialContract;
use Pollen\Social\Metabox\ChannelMetabox;
use Pollen\Social\SocialAwareTrait;
use tiFy\Support\Concerns\BootableTrait;
use tiFy\Support\Concerns\ParamsBagTrait;
use tiFy\Support\ParamsBag;
use tiFy\Support\Proxy\Url;
use tiFy\Support\Proxy\View;

class SocialChannelDriver implements SocialChannelDriverInterface
{
    use BootableTrait;
    use ParamsBagTrait;
    use SocialAwareTrait;

    /**
     * Nom de qualification.
     * @var string
     */
    protected $name = '';

    /**
     * Url de partage et indicateur de partage possible sur le réseau.
     * @see https://css-tricks.com/simple-social-sharing-links/
     * @see https://jonsuh.com/blog/social-share-links/
     * @var string
     */
    protected $sharer = '';

    /**
     * Paramètre de partage.
     * @var array
     */
    protected $share_params = [];

    /**
     * Instance du gestionnaire de gabarits d'affichage.
     * @var ViewEngine
     */
    protected $viewEngine;

    /**
     * @param SocialContract $socialManager
     */
    public function __construct(SocialContract $socialManager)
    {
        $this->setSocialManager($socialManager);
    }

    /**
     * @inheritDoc
     */
    public function __get(string $key)
    {
        return $this->params($key);
    }

    /**
     * @inheritDoc
     */
    public function __call(string $method, array $arguments)
    {
        try {
            return $this->params()->{$method}(...$arguments);
        } catch (Exception $e) {
            throw new BadMethodCallException(
                sprintf(
                    'SocialChannelDriver [%s] method call [%s] throws an exception: %s',
                    $this->getName(),
                    $method,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): SocialChannelDriverInterface
    {
        if (!$this->isBooted()) {
            events()->trigger("social.channel.booting", [$this->getName(), $this]);

            $this->parseParams()->setBooted();

            events()->trigger("social.channel.booted", [$this->getName(), $this]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return [
            /**
             * @var bool $active Activation de la prise en charge.
             */
            'active'          => false,
            /**
             * @var bool $admin Activation de l'administrabilité.
             */
            'admin'           => true,
            /**
             * @var bool $deeplink Activation de gestion de lien profond.
             */
            'deeplink'        => true,
            /**
             * @var string $icon Icone représentative.
             */
            'icon'            => '',
            /**
             * @var array $page_link_attrs Liste des attributs de configuration du lien vers la page du compte.
             */
            'page_link_attrs' => [],
            /**
             * @var string $option_name Nom d'enregistrement des attributs en base.
             */
            'option_name'     => $this->getOptionName(),
            /**
             * @var int $order Ordre d'affichage du lien vers la page du compte dans le menu.
             */
            'order'           => 0,
            /**
             *
             */
            'share'           => false,
            /**
             * @var string $title Intitulé de qualification du réseau.
             */
            'title'           => ucfirst($this->getName()),
            /**
             * @var string $uri Lien vers la page du compte
             */
            'uri'             => '',
            /**
             * @var array $viewer Liste des attributs de configuration du pilote d'affichage.
             */
            'viewer'          => [],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDeeplink(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getIcon(): string
    {
        return $this->get('icon')
            ?: call_user_func(
                $this->socialManager()->resources(),
                "/assets/dist/img/channel/{$this->getName()}/icon.svg"
            );
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getPageUrl(): string
    {
        return (string)$this->get('uri');
    }

    /**
     * @inheritDoc
     */
    public function getOrder(): int
    {
        return (int)$this->get('order');
    }

    /**
     * @inheritDoc
     */
    public function getOptionName(): string
    {
        return "tify_social_share[{$this->getOptionNameKey()}]";
    }

    /**
     * @inheritDoc
     */
    public function getOptionNameKey(): string
    {
        return $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function getShareUrl(?array $params = null): string
    {
        if (is_null($params) && empty($this->share_params)) {
            return '';
        }

        return $this->sharer
            ? Url::set($this->sharer)->with(array_merge($this->share_params, $params ?: []))->render() : '';
    }

    /**
     * @inheritDoc
     */
    public function getStatus(): string
    {
        return $this->isActive() && $this->hasUri() ? 'online' : ($this->isActive() ? 'warning' : 'offline');
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->get('title');
    }

    /**
     * @inheritDoc
     */
    public function hasAdmin(): bool
    {
        return $this->socialManager()->config('admin', true) && $this->get('admin', true);
    }

    /**
     * @inheritDoc
     */
    public function hasShare(): bool
    {
        return $this->isSharer() && filter_var($this->get('share'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @inheritDoc
     */
    public function hasUri(): bool
    {
        return !empty($this->get('uri'));
    }

    /**
     * @inheritDoc
     */
    public function isActive(): bool
    {
        $active = $this->get('active', false);

        return $active && ($active !== 'off');
    }

    /**
     * @inheritDoc
     */
    public function isAndroidOS(): bool
    {
        return (new MobileDetect())->is('AndroidOS');
    }

    /**
     * @inheritDoc
     */
    public function isMobile(): bool
    {
        return (new MobileDetect())->isMobile();
    }

    /**
     * @inheritDoc
     */
    public function isIOS(): bool
    {
        return (new MobileDetect())->is('iOS');
    }

    /**
     * @inheritDoc
     */
    public function isSharer(): bool
    {
        return !!$this->sharer;
    }

    /**
     * @inheritDoc
     */
    public function pageLink(array $attrs = []): string
    {
        if (!$this->isActive() || !$this->hasUri()) {
            return '';
        } else {
            $params = (new ParamsBag())->set(array_merge($this->get('page_link_attrs'), $attrs));

            if (!$params->has('tag')) {
                $params->set('tag', 'a');
            }

            if (!$params->has('content')) {
                $params->set(
                    'content',
                    ($params->get('icon', true) ? $this->getIcon() : '') .
                    ($params->get('title', true) ? $this->getTitle() : '')
                );
            }

            $params->set('attrs.href', $this->getPageUrl());

            if ($this->isMobile() && $this->get('deeplink') && ($deeplink = $this->getDeeplink())) {
                $params->set(
                    [
                        'attrs.data-control'  => 'social.deeplink',
                        'attrs.data-deeplink' => $deeplink,
                    ]
                );
            }

            if (!$params->has('attrs.class')) {
                $params->set('attrs.class', 'Social-link Social-link--' . $this->getName());
            } else {
                $params->set('attrs.class', sprintf($params->get('attrs.class', '%s'), $this->getName()));
            }

            if (!$params->has('attrs.title')) {
                $params->set('attrs.title', sprintf(__('Accéder à la page %s', 'tify'), $this->getTitle()));
            }

            if (!$params->has('attrs.target')) {
                $params->set('attrs.target', '_blank');
            }

            if (!$params->has('attrs.rel')) {
                $params->set('attrs.rel', 'noreferrer');
            }

            return $this->view('page-link', $params->all());
        }
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): self
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setName(string $name): SocialChannelDriverInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPostShare($post): SocialChannelDriverInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function view(?string $name = null, array $data = [])
    {
        if (is_null($this->viewEngine)) {
            $this->viewEngine = $this->socialManager()->containerHas('social.channel.view-engine')
                ? $this->socialManager()->containerGet('social.channel.view-engine') : View::getPlatesEngine();

            $directory = $this->socialManager()->resources('/views/channel/' . $this->getName());
            if (is_dir($directory)) {
                $this->viewEngine->setDirectory($directory);
            }

            $this->viewEngine->params(array_merge($this->get('viewer', []), ['channel' => $this]));
        }

        if (func_num_args() === 0) {
            return $this->viewEngine;
        }

        return $this->viewEngine->render($name, $data);
    }
}