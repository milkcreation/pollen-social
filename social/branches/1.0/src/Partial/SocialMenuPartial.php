<?php declare(strict_types=1);

namespace Pollen\Social\Partial;

use Illuminate\Support\Collection;
use Pollen\Social\Channels\SocialChannelDriverInterface;
use tiFy\Partial\PartialDriverInterface;

class SocialMenuPartial extends AbstractSocialPartialDriver
{
    /**
     * @inheritDoc
     */
    public function defaultParams(): array
    {
        return array_merge(parent::defaultParams(), [
            'classes' => []
        ]);
    }

    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        $this->set('viewer.directory', $this->socialManager()->resources('/views/partial/menu'));

        parent::parseParams();

        $defaultClasses = [
            'item'    => 'Social-menuChannel',
            'items'   => 'Social-menuChannels',
            'link'    => '%s'
        ];

        foreach ($defaultClasses as $k => $v) {
            $this->set("classes.{$k}", sprintf($this->get("classes.{$k}", '%s'), $v));
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (!$this->get('items', [])) {
            $this->set([
                'items' => (new Collection($this->socialManager()->getChannels()))
                    ->filter(function (SocialChannelDriverInterface $channel) {
                        return $channel->isActive() && $channel->hasUri();
                    })
                    ->sortBy(function (SocialChannelDriverInterface $channel) {
                        return $channel->getOrder();
                    })->all(),
            ]);
        }

        return parent::render();
    }
}
