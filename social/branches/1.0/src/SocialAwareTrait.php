<?php declare(strict_types=1);

namespace Pollen\Social;

use Exception;
use Pollen\Social\Contracts\SocialContract;

trait SocialAwareTrait
{
    /**
     * Instance du gestionnaire de réseaux sociaux.
     * @var SocialContract|null
     */
    protected $socialManager;

    /**
     * Récupération de l'instance du gestionnaire de réseaux sociaux.
     *
     * @return SocialContract|null
     */
    public function socialManager(): ?SocialContract
    {
        if (is_null($this->socialManager)) {
            try {
                $this->socialManager = Social::instance();
            } catch (Exception $e) {
                $this->socialManager;
            }
        }

        return $this->socialManager;
    }

    /**
     * Définition du gestionnaire de réseaux sociaux.
     *
     * @param SocialContract $socialManager
     *
     * @return static
     */
    public function setSocialManager(SocialContract $socialManager): self
    {
        $this->socialManager = $socialManager;

        return $this;
    }
}
