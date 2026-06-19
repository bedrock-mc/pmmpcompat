<?php

declare(strict_types=1);

namespace pocketmine\plugin;

use pocketmine\lang\Translatable;
use pocketmine\utils\Utils;
use pocketmine\utils\VersionString;

/**
 * Validates plugin metadata before bridge-side loading.
 */
final class PluginLoadabilityChecker
{
    public function __construct(private string $apiVersion)
    {
    }

    public function check(PluginDescription $description): ?Translatable
    {
        $name = $description->getName();
        if (stripos($name, 'pocketmine') !== false || stripos($name, 'minecraft') !== false || stripos($name, 'mojang') !== false) {
            return $this->reason('pocketmine.plugin.restrictedName', $name);
        }

        foreach ($description->getCompatibleApis() as $api) {
            if (!VersionString::isValidBaseVersion($api)) {
                return $this->reason('pocketmine.plugin.invalidAPI', $api);
            }
        }

        if (!ApiVersion::isCompatible($this->apiVersion, $description->getCompatibleApis())) {
            return $this->reason('pocketmine.plugin.incompatibleAPI', implode(', ', $description->getCompatibleApis()));
        }

        $ambiguousVersions = ApiVersion::checkAmbiguousVersions($description->getCompatibleApis());
        if (count($ambiguousVersions) > 0) {
            return $this->reason('pocketmine.plugin.ambiguousMinAPI', implode(', ', $ambiguousVersions));
        }

        $compatibleOs = $description->getCompatibleOperatingSystems();
        if ($compatibleOs !== [] && !in_array(Utils::getOS(), $compatibleOs, true)) {
            return $this->reason('pocketmine.plugin.incompatibleOS', implode(', ', $compatibleOs));
        }

        foreach (Utils::stringifyKeys($description->getRequiredExtensions()) as $extensionName => $versionConstraints) {
            if (!extension_loaded($extensionName)) {
                return $this->reason('pocketmine.plugin.extensionNotLoaded', $extensionName);
            }
            $gotVersion = phpversion($extensionName);
            $gotVersion = $gotVersion === false ? '**UNKNOWN**' : $gotVersion;

            foreach ($versionConstraints as $k => $constraint) {
                $constraint = (string) $constraint;
                if ($constraint === '*') {
                    continue;
                }
                if ($constraint === '') {
                    return $this->reason('pocketmine.plugin.emptyExtensionVersionConstraint', $extensionName, (string) $k);
                }
                $matches = $this->matchesExtensionConstraint($gotVersion, $constraint);
                if ($matches === null) {
                    return $this->reason('pocketmine.plugin.invalidExtensionVersionConstraint', $extensionName, $constraint);
                }
                if (!$matches) {
                    return $this->reason('pocketmine.plugin.incompatibleExtensionVersion', $extensionName, $gotVersion, $constraint);
                }
            }
        }

        return null;
    }

    private function matchesExtensionConstraint(string $gotVersion, string $constraint): ?bool
    {
        foreach (['<=', 'le', '<>', '!=', 'ne', '<', 'lt', '==', '=', 'eq', '>=', 'ge', '>', 'gt'] as $comparator) {
            if (str_starts_with($constraint, $comparator)) {
                return version_compare($gotVersion, substr($constraint, strlen($comparator)), $comparator);
            }
        }
        return null;
    }

    private function reason(string $key, string ...$params): Translatable
    {
        return new Translatable($key, $params);
    }
}
