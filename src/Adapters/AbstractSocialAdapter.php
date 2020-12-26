<?php declare(strict_types=1);

namespace Pollen\Social\Adapters;

use Pollen\Social\Contracts\SocialContract;
use Pollen\Social\SocialAwareTrait;

abstract class AbstractSocialAdapter implements AdapterInterface
{
    use SocialAwareTrait;

    /**
     * @param SocialContract $socialManager
     */
    public function __construct(SocialContract $socialManager)
    {
        $this->setSocialManager($socialManager);
    }
}