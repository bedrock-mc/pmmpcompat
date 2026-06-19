<?php

declare(strict_types=1);

namespace pocketmine\network\mcpe\handler;

/**
 * Builds local item-stack request responses from changed container slots.
 */
final class ItemStackResponseBuilder
{
    /** @var array<int, array<int, int>> */
    private array $changedSlots = [];

    public function __construct(
        private int $requestId,
        private mixed $inventoryManager = null
    ) {}

    public function addSlot(int $containerInterfaceId, int $slotId): void
    {
        $this->changedSlots[$containerInterfaceId][$slotId] = $slotId;
    }

    /** @return array<int, list<int>> */
    public function getChangedSlots(): array
    {
        $result = [];
        foreach ($this->changedSlots as $containerId => $slots) {
            $result[$containerId] = array_values($slots);
        }
        return $result;
    }

    /** @return array{result: string, requestId: int, containers: list<array{containerId: int, slots: list<array<string, mixed>>}>} */
    public function build(): array
    {
        $containers = [];
        foreach ($this->changedSlots as $containerInterfaceId => $slotIds) {
            $slots = [];
            foreach ($slotIds as $slotId) {
                $slots[] = $this->buildSlot($containerInterfaceId, $slotId);
            }
            $containers[] = [
                'containerId' => $containerInterfaceId,
                'slots' => $slots,
            ];
        }

        return [
            'result' => 'ok',
            'requestId' => $this->requestId,
            'containers' => $containers,
        ];
    }

    /** @return array<string, mixed> */
    private function buildSlot(int $containerInterfaceId, int $slotId): array
    {
        $slot = [
            'slot' => $slotId,
            'hotbarSlot' => $slotId,
            'count' => 0,
            'stackId' => 0,
            'customName' => '',
            'durabilityCorrection' => 0,
        ];

        if ($this->inventoryManager === null || !method_exists($this->inventoryManager, 'locateWindowAndSlot')) {
            return $slot;
        }

        try {
            [$windowId, $translatedSlot] = ItemStackContainerIdTranslator::translate(
                $containerInterfaceId,
                method_exists($this->inventoryManager, 'getCurrentWindowId') ? $this->inventoryManager->getCurrentWindowId() : 0,
                $slotId
            );
            $windowAndSlot = $this->inventoryManager->locateWindowAndSlot($windowId, $translatedSlot);
        } catch (\Throwable) {
            return $slot;
        }

        if (!is_array($windowAndSlot) || count($windowAndSlot) < 2) {
            return $slot;
        }

        [$inventory, $inventorySlot] = $windowAndSlot;
        if (is_object($inventory) && method_exists($inventory, 'getItem')) {
            $item = $inventory->getItem($inventorySlot);
            $slot['count'] = is_object($item) && method_exists($item, 'getCount') ? $item->getCount() : 0;
            $slot['customName'] = is_object($item) && method_exists($item, 'getCustomName') ? $item->getCustomName() : '';
            $slot['durabilityCorrection'] = is_object($item) && method_exists($item, 'getDamage') ? $item->getDamage() : 0;
        }
        if (method_exists($this->inventoryManager, 'getItemStackInfo')) {
            $info = $this->inventoryManager->getItemStackInfo($inventory, $inventorySlot);
            $slot['stackId'] = is_object($info) && method_exists($info, 'getStackId') ? $info->getStackId() : 0;
        }

        return $slot;
    }
}
