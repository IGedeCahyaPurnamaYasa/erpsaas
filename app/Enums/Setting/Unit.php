<?php

namespace App\Enums\Setting;

use App\Enums\Concerns\ParsesEnum;
use Filament\Support\Contracts\HasLabel;

enum Unit: string implements HasLabel
{
    use ParsesEnum;

    case pcs = 'picis';
    case kg = 'kilo gram';
    case gr = 'gram';
    case l = 'liter';
    case ml = 'mili liter';

    public const DEFAULT = self::pcs->value;

    public function getLabel(): ?string
    {
        return translate($this->name);
    }
}
