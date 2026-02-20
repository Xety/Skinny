<?php
declare(strict_types=1);

namespace Skinny\Service;

/**
 * Service de rate limiting pour les commandes
 */
class RateLimiter
{
    /**
     * Stockage des timestamps d'utilisation
     *
     * @var array<string, float>
     */
    private array $timestamps = [];

    /**
     * Tente une action avec rate limiting
     *
     * @param string $key Clé unique (ex: "userId:command")
     * @param int $cooldownSeconds Durée du cooldown en secondes
     *
     * @return bool True si l'action est autorisée
     */
    public function attempt(string $key, int $cooldownSeconds): bool
    {
        $now = microtime(true);

        if (isset($this->timestamps[$key])) {
            $elapsed = $now - $this->timestamps[$key];

            if ($elapsed < $cooldownSeconds) {
                return false;
            }
        }

        $this->timestamps[$key] = $now;
        return true;
    }

    /**
     * Retourne le temps restant avant la prochaine tentative autorisée
     *
     * @param string $key
     *
     * @return int Secondes restantes
     */
    public function getRemainingTime(string $key): int
    {
        if (!isset($this->timestamps[$key])) {
            return 0;
        }

        $elapsed = microtime(true) - $this->timestamps[$key];
        return max(0, (int)ceil($elapsed));
    }

    /**
     * Réinitialise le cooldown pour une clé
     *
     * @param string $key
     *
     * @return void
     */
    public function reset(string $key): void
    {
        unset($this->timestamps[$key]);
    }

    /**
     * Nettoie les entrées expirées
     *
     * @param int $maxAge Âge maximum en secondes
     *
     * @return int Nombre d'entrées supprimées
     */
    public function cleanup(int $maxAge = 3600): int
    {
        $now = microtime(true);
        $count = 0;

        foreach ($this->timestamps as $key => $timestamp) {
            if (($now - $timestamp) > $maxAge) {
                unset($this->timestamps[$key]);
                $count++;
            }
        }

        return $count;
    }
}
