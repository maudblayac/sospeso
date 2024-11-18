<?php
namespace App\Enum;

enum Status: string
{
    case EN_ATTENTE = 'en_attente';
    // case EN_COURS = 'en cours';
    case VERIFIE = 'verifie';
    case ARCHIVE = 'archive';
}