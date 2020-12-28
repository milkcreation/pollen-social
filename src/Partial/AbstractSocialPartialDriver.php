<?php declare(strict_types=1);

namespace Pollen\Social\Partial;

use Pollen\Social\SocialAwareTrait;
use Pollen\Social\Contracts\SocialContract;
use tiFy\Partial\Contracts\PartialContract;
use tiFy\Partial\PartialDriver;

abstract class AbstractSocialPartialDriver extends PartialDriver implements PartialDriverInterface
{
    use SocialAwareTrait;

    /**
     * @param SocialContract $socialManager
     * @param PartialContract $partialManager
     */
    public function __construct(SocialContract $socialManager, PartialContract $partialManager)
    {
        $this->setSocialManager($socialManager);

        parent::__construct($partialManager);
    }
}