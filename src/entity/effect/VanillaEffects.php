<?php

declare(strict_types=1);

namespace pocketmine\entity\effect;

use pocketmine\color\Color;

class VanillaEffects
{
    /** @return array<string, Effect> */
    public static function getAll(): array
    {
        return [
            'speed' => new SpeedEffect('Speed', new Color(124, 175, 198)),
            'slowness' => new SlownessEffect('Slowness', new Color(90, 108, 129), true),
            'haste' => new Effect('Haste', new Color(217, 192, 67)),
            'strength' => new Effect('Strength', new Color(147, 36, 35)),
            'instant_health' => new InstantHealthEffect('Instant Health', new Color(248, 36, 35)),
            'instant_damage' => new InstantDamageEffect('Instant Damage', new Color(67, 10, 9), true),
            'jump_boost' => new Effect('Jump Boost', new Color(34, 255, 76)),
            'nausea' => new Effect('Nausea', new Color(85, 29, 74), true),
            'regeneration' => new RegenerationEffect('Regeneration', new Color(205, 92, 171)),
            'resistance' => new Effect('Resistance', new Color(153, 69, 58)),
            'fire_resistance' => new Effect('Fire Resistance', new Color(228, 154, 58)),
            'water_breathing' => new Effect('Water Breathing', new Color(46, 82, 153)),
            'invisibility' => new InvisibilityEffect('Invisibility', new Color(127, 131, 146)),
            'blindness' => new Effect('Blindness', new Color(31, 31, 35), true),
            'night_vision' => new Effect('Night Vision', new Color(31, 31, 161)),
            'hunger' => new HungerEffect('Hunger', new Color(88, 118, 83), true),
            'weakness' => new Effect('Weakness', new Color(72, 77, 72), true),
            'poison' => new PoisonEffect('Poison', new Color(78, 147, 49), true),
            'wither' => new WitherEffect('Wither', new Color(53, 42, 39), true),
            'health_boost' => new HealthBoostEffect('Health Boost', new Color(248, 125, 35)),
            'absorption' => new AbsorptionEffect('Absorption', new Color(37, 82, 165)),
            'saturation' => new SaturationEffect('Saturation', new Color(248, 36, 35)),
            'levitation' => new LevitationEffect('Levitation', new Color(206, 255, 255)),
        ];
    }
}
