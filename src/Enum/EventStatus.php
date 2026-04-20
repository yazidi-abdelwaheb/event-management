<?php

namespace App\Enum;

enum EventStatus: string
{
    case UPCOMING = 'upcoming';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    // 🎯 Label affichage propre
    public function label(): string
    {
        return match($this) {
            self::UPCOMING => 'Up Coming',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    
    public function color(): string
    {
        return match($this) {
            self::UPCOMING => '#EAF3DE',
            self::IN_PROGRESS => '#FFF4D6',
            self::COMPLETED => '#E0F7FA',
        };
    }


    public function textColor(): string
    {
        return match($this) {
            self::UPCOMING => '#3B6D11',
            self::IN_PROGRESS => '#B78103',
            self::COMPLETED => '#0B7285',
        };
    }
}