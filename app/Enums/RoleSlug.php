<?php

namespace App\Enums;

enum RoleSlug: string
{
    case Employe = 'employe';
    case Agent = 'agent';
    case Validateur = 'validateur';
    case Administrateur = 'administrateur';
}
