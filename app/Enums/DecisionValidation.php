<?php

namespace App\Enums;

enum DecisionValidation: string
{
    case Approuve = 'approuve';
    case Rejete = 'rejete';
    case ComplementDemande = 'complement_demande';
}
