<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Local executor for item-stack requests.
 *
 * This does not mutate Dragonfly state. It records requested slot changes and
 * returns PMMP-shaped response arrays for compatibility smoke tests and plugin
 * code paths that only need request bookkeeping.
 */
class ItemStackRequestExecutor
{
    private ItemStackResponseBuilder $responseBuilder;

    /** @var list<array<string, mixed>> */
    private array $actions = [];

    public function __construct(
        private mixed $player = null,
        private mixed $inventoryManager = null,
        private mixed $request = null
    ) {
        $this->responseBuilder = new ItemStackResponseBuilder($this->requestId($request), $inventoryManager);
    }

    /** @return array{result: string, requestId: int, containers: list<array{containerId: int, slots: list<array<string, mixed>>}>} */
    public function buildItemStackResponse(): array
    {
        foreach ($this->extractActions($this->request) as $action) {
            $this->recordAction($action);
        }

        return $this->responseBuilder->build();
    }

    /** @return array{player: mixed, actions: list<array<string, mixed>>} */
    public function generateInventoryTransaction(): array
    {
        return [
            'player' => $this->player,
            'actions' => $this->actions,
        ];
    }

    public function getItemStackResponseBuilder(): ItemStackResponseBuilder
    {
        return $this->responseBuilder;
    }

    private function requestId(mixed $request): int
    {
        if (is_object($request) && method_exists($request, 'getRequestId')) {
            return (int) $request->getRequestId();
        }
        if (is_array($request) && isset($request['requestId'])) {
            return (int) $request['requestId'];
        }
        return 0;
    }

    /** @return iterable<mixed> */
    private function extractActions(mixed $request): iterable
    {
        if (is_object($request) && method_exists($request, 'getActions')) {
            return $request->getActions();
        }
        if (is_array($request) && isset($request['actions']) && is_iterable($request['actions'])) {
            return $request['actions'];
        }
        return [];
    }

    private function recordAction(mixed $action): void
    {
        $source = $this->slotInfo($action, 'source');
        $destination = $this->slotInfo($action, 'destination');
        $actionInfo = [
            'type' => is_object($action) ? $action::class : get_debug_type($action),
            'source' => $source,
            'destination' => $destination,
        ];
        $this->actions[] = $actionInfo;

        foreach ([$source, $destination] as $slot) {
            if ($slot !== null) {
                $this->responseBuilder->addSlot($slot['containerId'], $slot['slotId']);
            }
        }
    }

    /** @return array{containerId: int, slotId: int}|null */
    private function slotInfo(mixed $action, string $role): ?array
    {
        $candidate = null;
        if (is_array($action)) {
            $candidate = $action[$role] ?? null;
        } elseif (is_object($action)) {
            $method = 'get' . ucfirst($role);
            $candidate = method_exists($action, $method) ? $action->{$method}() : null;
        }

        if ($candidate === null) {
            return null;
        }

        if (is_array($candidate)) {
            return [
                'containerId' => (int) ($candidate['containerId'] ?? $candidate['container'] ?? 0),
                'slotId' => (int) ($candidate['slotId'] ?? $candidate['slot'] ?? 0),
            ];
        }

        if (is_object($candidate)) {
            $containerId = method_exists($candidate, 'getContainerId') ? $candidate->getContainerId() : 0;
            $slotId = method_exists($candidate, 'getSlotId') ? $candidate->getSlotId() : 0;
            return ['containerId' => (int) $containerId, 'slotId' => (int) $slotId];
        }

        throw new ItemStackRequestProcessException("Unsupported $role slot info");
    }
}
