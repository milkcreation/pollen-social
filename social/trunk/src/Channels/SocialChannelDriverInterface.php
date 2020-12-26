<?php declare(strict_types=1);

namespace Pollen\Social\Channels;

use tiFy\Contracts\View\PlatesEngine;
use tiFy\Wordpress\Contracts\Query\QueryPost;

/**
 * @mixin \Pollen\Social\SocialAwareTrait
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ParamsBagTrait
 * @mixin \tiFy\Support\ParamsBag
 */
interface SocialChannelDriverInterface
{
    /**
     * Récupération des paramètres.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key);

    /**
     * Délégation d'appel des méthodes du ParamBag.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call(string $method, array $arguments);

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): SocialChannelDriverInterface;

    /**
     * Récupération du lien profond.
     *
     * @return string
     */
    public function getDeeplink(): string;

    /**
     * Récupération de l'icône représentative.
     *
     * @return string
     */
    public function getIcon(): string;

    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Récupération de l'intitulé de qualification.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Récupération de l'url de la page.
     *
     * @return string
     */
    public function getPageUrl(): string;

    /**
     * Ordre d'affichage dans la liste des réseaux pris en charge.
     *
     * @return int
     */
    public function getOrder(): int;

    /**
     * Récupération du nom d'enregistrement des données en base.
     *
     * @return string
     */
    public function getOptionName(): string;

    /**
     * Récupération de la clé de qualification du nom d'enregistrement des données en base.
     *
     * @return string
     */
    public function getOptionNameKey(): string;

    /**
     * Récupération de l'url de partage.
     *
     * @param array|null $params Liste des paramètres de partage.
     *
     * @return string
     */
    public function getShareUrl(?array $params = null): string;

    /**
     * Récupération du statut d'affichage du réseau.
     *
     * @return string online|warning|offline
     */
    public function getStatus(): string;

    /**
     * Vérification d'activation de l'administrabilité du réseau.
     *
     * @return bool
     */
    public function hasAdmin(): bool;

    /**
     * Vérification d'activation de partage.
     *
     * @return bool
     */
    public function hasShare(): bool;

    /**
     * Vérification de l'existance d'une url vers la page du compte du réseau.
     *
     * @return bool
     */
    public function hasUri(): bool;

    /**
     * Vérification d'activation de la prise en charge du réseau.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Détection Mobile Android.
     *
     * @return bool
     */
    public function isAndroidOS(): bool;

    /**
     * Détection Mobile.
     *
     * @return bool
     */
    public function isMobile(): bool;

    /**
     * Détection Mobile Apple.
     *
     * @return bool
     */
    public function isIOS(): bool;

    /**
     * Indique si le partage est possible sur le réseau.
     *
     * @return bool
     */
    public function isSharer(): bool;

    /**
     * Lien vers la page de profil du réseau social.
     *
     * @param array $attrs Liste des attributs de configuration du lien.
     *
     * @return string
     */
    public function pageLink(array $attrs = []): string;

    /**
     * Définition du nom de qualification.
     *
     * @param string $name
     *
     * @return static
     */
    public function setName(string $name): SocialChannelDriverInterface;

    /**
     * Définition des paramètres de partage d'une publication.
     *
     * @param QueryPost $post
     *
     * @return static
     */
    public function setPostShare(QueryPost $post): SocialChannelDriverInterface;

    /**
     * Instance du gestionnaire de gabarits d'affichage ou instance du gabarit d'affichage.
     *
     * @param string|null $name Nom de qualification du gabarit d'affichage.
     * @param array $data Liste des paramètres passés en arguments au gabarit d'affichage.
     *
     * @return PlatesEngine|string
     */
    public function view(?string $name = null, array $data = []);
}
