<?php
declare(strict_types=1);

namespace Skinny\Event;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * EventDispatcher - Système d'événements avec priorités et propagation stoppable
 */
class EventDispatcher
{
    /**
     * Les listeners enregistrés par événement
     *
     * @var array<string, array<int, array<callable>>>
     */
    private array $listeners = [];

    /**
     * Logger PSR-3
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface|null $logger
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Abonne un listener à un événement
     *
     * @param string $eventName Le nom de l'événement
     * @param callable $listener Le listener à appeler
     * @param int $priority La priorité (plus élevé = exécuté en premier)
     *
     * @return self
     */
    public function subscribe(string $eventName, callable $listener, int $priority = 0): self
    {
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [];
        }

        if (!isset($this->listeners[$eventName][$priority])) {
            $this->listeners[$eventName][$priority] = [];
        }

        $this->listeners[$eventName][$priority][] = $listener;

        // Trier par priorité décroissante
        krsort($this->listeners[$eventName], SORT_NUMERIC);

        return $this;
    }

    /**
     * Désabonne un listener d'un événement
     *
     * @param string $eventName
     * @param callable $listener
     *
     * @return self
     */
    public function unsubscribe(string $eventName, callable $listener): self
    {
        if (!isset($this->listeners[$eventName])) {
            return $this;
        }

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            foreach ($listeners as $key => $registeredListener) {
                if ($registeredListener === $listener) {
                    unset($this->listeners[$eventName][$priority][$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Dispatch un événement à tous les listeners
     *
     * @param string $eventName
     * @param Event $event
     *
     * @return Event
     */
    public function dispatch(string $eventName, Event $event): Event
    {
        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        $this->logger->debug("Dispatching event: {$eventName}");

        foreach ($this->listeners[$eventName] as $priority => $listeners) {
            foreach ($listeners as $listener) {
                if ($event->isPropagationStopped()) {
                    $this->logger->debug("Event propagation stopped for: {$eventName}");
                    break 2;
                }

                try {
                    $listener($event);
                } catch (\Throwable $e) {
                    $this->logger->error("Error in event listener for {$eventName}: {$e->getMessage()}", [
                        'exception' => $e,
                        'listener' => is_array($listener)
                            ? get_class($listener[0]) . '::' . $listener[1]
                            : 'closure'
                    ]);

                    // Ne pas arrêter la propagation en cas d'erreur d'un listener
                }
            }
        }

        return $event;
    }

    /**
     * Vérifie si un événement a des listeners
     *
     * @param string $eventName
     *
     * @return bool
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }

    /**
     * Retourne tous les listeners pour un événement
     *
     * @param string $eventName
     *
     * @return array
     */
    public function getListeners(string $eventName): array
    {
        if (!isset($this->listeners[$eventName])) {
            return [];
        }

        $flatListeners = [];
        foreach ($this->listeners[$eventName] as $listeners) {
            $flatListeners = array_merge($flatListeners, $listeners);
        }

        return $flatListeners;
    }
}
