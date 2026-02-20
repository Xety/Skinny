<?php
declare(strict_types=1);

namespace Skinny\Event;

use Skinny\Network\Wrapper;

/**
 * Classe de base pour tous les événements
 */
class Event
{
    /**
     * Le nom de l'événement
     */
    private string $name;

    /**
     * Le Wrapper instance
     */
    private ?Wrapper $wrapper;

    /**
     * Données additionnelles de l'événement
     */
    private array $data;

    /**
     * Flag de propagation stoppée
     */
    private bool $propagationStopped = false;

    /**
     * Constructor
     *
     * @param string $name
     * @param Wrapper|null $wrapper
     * @param array $data
     */
    public function __construct(string $name, ?Wrapper $wrapper = null, array $data = [])
    {
        $this->name = $name;
        $this->wrapper = $wrapper;
        $this->data = $data;
    }

    /**
     * Retourne le nom de l'événement
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retourne le Wrapper
     *
     * @return Wrapper|null
     */
    public function getWrapper(): ?Wrapper
    {
        return $this->wrapper;
    }

    /**
     * Retourne les données de l'événement
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Récupère une donnée spécifique
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Définit une donnée
     *
     * @param string $key
     * @param mixed $value
     *
     * @return self
     */
    public function set(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Arrête la propagation de l'événement
     *
     * @return self
     */
    public function stopPropagation(): self
    {
        $this->propagationStopped = true;
        return $this;
    }

    /**
     * Vérifie si la propagation est stoppée
     *
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
