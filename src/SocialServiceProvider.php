<?php declare(strict_types=1);

namespace Pollen\Social;

use Pollen\Social\Adapters\WordpressAdapter;
use Pollen\Social\Channels\DailymotionChannel;
use Pollen\Social\Channels\FacebookChannel;
use Pollen\Social\Channels\GooglePlusChannel;
use Pollen\Social\Channels\InstagramChannel;
use Pollen\Social\Channels\LinkedinChannel;
use Pollen\Social\Channels\PinterestChannel;
use Pollen\Social\Channels\TwitterChannel;
use Pollen\Social\Channels\ViadeoChannel;
use Pollen\Social\Channels\VimeoChannel;
use Pollen\Social\Channels\YoutubeChannel;
use Pollen\Social\Channels\SocialChannelView;
use Pollen\Social\Contracts\SocialContract;
use Pollen\Social\Partial\SocialMenuPartial;
use Pollen\Social\Partial\SocialSharePartial;
use tiFy\Container\ServiceProvider;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Support\Proxy\View;

class SocialServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * {@internal Permet le chargement différé des services qualifié.}
     * @var string[]
     */
    protected $provides = [
        SocialContract::class,
        DailymotionChannel::class,
        FacebookChannel::class,
        GooglePlusChannel::class,
        InstagramChannel::class,
        LinkedinChannel::class,
        PinterestChannel::class,
        TwitterChannel::class,
        ViadeoChannel::class,
        VimeoChannel::class,
        YoutubeChannel::class,
        'social.channel.view-engine',
        'social.view-engine',
    ];

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        events()->listen('wp.booted', function () {
            /** @var SocialContract $social */
            $social = $this->getContainer()->get(SocialContract::class);
            $social->setAdapter($this->getContainer()->get(WordpressAdapter::class))->boot();
        });
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(SocialContract::class, function () {
            return new Social(config('social', []), $this->getContainer());
        });

        $this->registerAdapters();
        $this->registerChannels();
        $this->registerChannelView();
        $this->registerPartialDrivers();
        $this->registerView();
    }

    /**
     * Déclaration des adapteurs.
     *
     * @return void
     */
    public function registerAdapters(): void
    {
        $this->getContainer()->share(WordpressAdapter::class, function () {
            return new WordpressAdapter($this->getContainer()->get(SocialContract::class));
        });
    }

    /**
     * Déclaration des pilotes de canaux de réseaux sociaux.
     *
     * @return void
     */
    public function registerChannels(): void
    {
        $this->getContainer()->add(DailymotionChannel::class, function () {
            return new DailymotionChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(FacebookChannel::class, function () {
            return new FacebookChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(GooglePlusChannel::class, function () {
            return new GooglePlusChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(InstagramChannel::class, function () {
            return new InstagramChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(LinkedinChannel::class, function () {
            return new LinkedinChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(PinterestChannel::class, function () {
            return new PinterestChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(TwitterChannel::class, function () {
            return new TwitterChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(ViadeoChannel::class, function () {
            return new ViadeoChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(VimeoChannel::class, function () {
            return new VimeoChannel($this->getContainer()->get(SocialContract::class));
        });

        $this->getContainer()->add(YoutubeChannel::class, function () {
            return new YoutubeChannel($this->getContainer()->get(SocialContract::class));
        });
    }

    /**
     * Déclaration du gestionnaire d'affichage de canal de réseau social.
     *
     * @return void
     */
    public function registerChannelView(): void
    {
        $this->getContainer()->share('social.channel.view-engine', function () {
            /** @var SocialContract $social */
            $social = $this->getContainer()->get(SocialContract::class);

            return View::getPlatesEngine(array_merge([
                'directory' => $social->resources('views/channel'),
                'factory'   => SocialChannelView::class,
            ]));
        });
    }

    /**
     * Déclaration des pilotes de portions d'affichage.
     *
     * @return void
     */
    public function registerPartialDrivers(): void
    {
        $this->getContainer()->add(SocialMenuPartial::class, function () {
            return new SocialMenuPartial(
                $this->getContainer()->get(SocialContract::class),
                $this->getContainer()->get(PartialContract::class)
            );
        });

        $this->getContainer()->add(SocialSharePartial::class, function () {
            return new SocialSharePartial(
                $this->getContainer()->get(SocialContract::class),
                $this->getContainer()->get(PartialContract::class)
            );
        });
    }

    /**
     * Déclaration du gestionnaire d'affichage.
     *
     * @return void
     */
    public function registerView(): void
    {
        $this->getContainer()->share('social.view-engine', function () {
            /** @var SocialContract $social */
            $social = $this->getContainer()->get(SocialContract::class);

            return View::getPlatesEngine([
                'directory' => $social->resources('views'),
            ]);
        });
    }
}
