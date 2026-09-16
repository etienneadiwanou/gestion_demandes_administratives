<?php

namespace App\Enums;

enum StatutDemande: string
{
    case Brouillon = 'brouillon';
    case Soumise = 'soumise';
    case EnVerification = 'en_verification';
    case ComplementDemande = 'complement_demande';
    case EnValidation = 'en_validation';
    case Approuvee = 'approuvee';
    case Rejetee = 'rejetee';
    case Archivee = 'archivee';
}
