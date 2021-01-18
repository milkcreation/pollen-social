<?php

declare(strict_types=1);

namespace Pollen\Social\Partial;

use Illuminate\Support\Collection;
use Pollen\Social\Channels\SocialChannelDriverInterface;
use tiFy\Wordpress\Query\QueryPost;

class SocialSharePartial extends AbstractSocialPartialDriver
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            'post'    => null,
            /**
             * Paramètres personnalisé de l'url de partage selon le réseau .
             * @var array
             */
            'channel' => [],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if ($post = ($p = $this->get('post', null)) instanceof QueryPost ? $p : QueryPost::create($p)) {
            $this->set([
                'items' => (new Collection($this->socialManager()->getChannels()))
                    ->filter(function (SocialChannelDriverInterface $channel) use ($post) {
                        if ($channel->isActive() && $channel->hasShare()) {
                            $channel->setPostShare($post);

                            return true;
                        }

                        return false;
                    })
                    ->sortBy(function (SocialChannelDriverInterface $channel) {
                        return $channel->getOrder();
                    })->all(),
                'post'  => $post,
            ]);

            return parent::render();
        } else {
            return '';
        }
    }

    /**
     * @inheritDoc
     */
    public function viewDirectory(): string
    {
        return $this->socialManager()->resources('/views/partial/share');
    }
}