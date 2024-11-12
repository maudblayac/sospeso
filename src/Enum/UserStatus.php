<?php
namespace App\Enum;

enum UserStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case VERIFIE = 'verifie';
    case ARCHIVE = 'archive';
}