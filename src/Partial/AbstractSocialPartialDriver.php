<?php declare(strict_types=1);

namespace Pollen\Social\Partial;

use tiFy\Contracts\Partial\Partial as PartialManagerContract;
use Pollen\Social\SocialAwareTrait;
use Pollen\Social\Contracts\SocialContract;
use tiFy\Partial\PartialDriver;

abstract class AbstractSocialPartialDriver extends PartialDriver implements PartialDriverInterface
{
    use SocialAwareTrait;

    /**
     * @param SocialContract $socialManager
     * @param PartialManagerContract $partialManager
     */
    public function __construct(SocialContract $socialManager, PartialManagerContract $partialManager)
    {
        $this->setSocialManager($socialManager);

        parent::__construct($partialManager);
    }
}