<?php declare(strict_types=1);

namespace Pollen\Social\Contracts;

use Pollen\Social\Adapters\AdapterInterface;
use Pollen\Social\Channels\SocialChannelDriverInterface;
use tiFy\Contracts\Filesystem\LocalFilesystem;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Contracts\View\Engine as ViewEngine;

/**
 * @mixin \tiFy\Support\Concerns\BootableTrait
 * @mixin \tiFy\Support\Concerns\ContainerAwareTrait
 */
interface SocialContract
{
    /**
     * Récupération de l'instance.
     *
     * @return static
     */
    public static function instance(): SocialContract;

    /**
     * Chargement.
     *
     * @return static
     */
    public function boot(): SocialContract;

    /**
     * Récupération de paramètre|Définition de paramètres|Instance du gestionnaire de paramètre.
     *
     * @param string|array|null $key Clé d'indice du paramètre à récupérer|Liste des paramètre à définir.
     * @param mixed $default Valeur de retour par défaut lorsque la clé d'indice est une chaine de caractère.
     *
     * @return ParamsBag|int|string|array|object
     */
    public function config($key = null, $default = null);

    /**
     * Récupération de l'instance de l'adapteur.
     *
     * @return AdapterInterface|null
     */
    public function getAdapter(): ?AdapterInterface;

    /**
     * Récupération de l'instance d'un réseau déclaré.
     *
     * @param string $name Nom de qualification du réseau.
     *
     * @return SocialChannelDriverInterface|null
     */
    public function getChannel(string $name): ?SocialChannelDriverInterface;

    /**
     * Rendu d'affichage d'un lien vers la page du compte d'un réseau.
     *
     * @param string $name Nom de qualification du réseau.
     * @param array $attrs Liste des attributs de configuration personnalisé.
     *
     * @return string
     */
    public function getChannelLink(string $name, array $attrs = []): string;

    /**
     * Récupération de la liste des instances de réseaux déclarés.
     *
     * @return SocialChannelDriverInterface[]|array
     */
    public function getChannels(): array;

    /**
     * Récupération d'un service fourni par le conteneur d'injection de dépendance.
     *
     * @param string $name
     *
     * @return callable|object|string|null
     */
    public function getProvider(string $name);

    /**
     * Chargement d'un pilote de réseaux déclarés.
     *
     * @param string $name
     *
     * @return SocialChannelDriverInterface
     */
    public function loadChannel(string $name): ?SocialChannelDriverInterface;

    /**
     * Chargement des pilotes de réseaux déclarés.
     *
     * @return static
     */
    public function loadChannels(): SocialContract;

    /**
     * Déclaration d'un réseau à liste des réseaux déclarés.
     *
     * @param string $name
     * @param string|array|SocialChannelDriverInterface $channelDefinition
     *
     * @return static
     */
    public function registerChannel(string $name, $channelDefinition): SocialContract;

    /**
     * Chemin absolu vers une ressources (fichier|répertoire).
     *
     * @param string|null $path Chemin relatif vers la ressource.
     *
     * @return LocalFilesystem|string|null
     */
    public function resources(?string $path = null);

    /**
     * Définition de l'adapteur associé.
     *
     * @param AdapterInterface $adapter
     *
     * @return static
     */
    public function setAdapter(AdapterInterface $adapter): SocialContract;

    /**
     * Définition des paramètres de configuration.
     *
     * @param array $attrs Liste des attributs de configuration.
     *
     * @return static
     */
    public function setConfig(array $attrs): SocialContract;

    /**
     * Instance du gestionnaire de gabarits d'affichage ou rendu du gabarit d'affichage.
     *
     * @param string|null $name Nom de qualification du gabarit.
     * @param array $data Liste des variables passées en argument.
     *
     * @return ViewEngine|string
     */
    public function view(?string $name = null, array $data = []);
}
