<?php

use App\Enums\StatutDemande;
use App\Models\ChampDemande;
use App\Models\Department;
use App\Models\Role;
use App\Models\TypeDemande;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

function creerUtilisateur(string $roleSlug): User
{
    $role = Role::firstOrCreate(['slug' => $roleSlug], ['nom' => ucfirst($roleSlug)]);
    $department = Department::factory()->create();

    return User::factory()->create([
        'role_id' => $role->id,
        'department_id' => $department->id,
    ]);
}

function creerTypeDemandeAvecChampObligatoire(): TypeDemande
{
    $type = TypeDemande::factory()->create();

    ChampDemande::factory()->create([
        'type_demande_id' => $type->id,
        'nom_technique' => 'motif',
        'obligatoire' => true,
    ]);

    return $type;
}

it('permet à un employé de créer une demande en brouillon', function () {
    $employe = creerUtilisateur('employe');
    $type = creerTypeDemandeAvecChampObligatoire();

    Sanctum::actingAs($employe);

    $response = $this->postJson('/api/demandes', [
        'type_demande_id' => $type->id,
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.statut', StatutDemande::Brouillon->value);

    $this->assertDatabaseHas('demandes', [
        'user_id' => $employe->id,
        'statut' => StatutDemande::Brouillon->value,
    ]);
});

it('refuse la soumission si un champ obligatoire est manquant', function () {
    $employe = creerUtilisateur('employe');
    $type = creerTypeDemandeAvecChampObligatoire();

    Sanctum::actingAs($employe);

    $demande = $this->postJson('/api/demandes', [
        'type_demande_id' => $type->id,
    ])->json('data');

    $response = $this->postJson("/api/demandes/{$demande['id']}/submit");

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('valeurs');
});

it('soumet correctement une demande une fois les champs obligatoires remplis', function () {
    $employe = creerUtilisateur('employe');
    $type = creerTypeDemandeAvecChampObligatoire();
    $champ = $type->champs()->first();

    Sanctum::actingAs($employe);

    $demande = $this->postJson('/api/demandes', [
        'type_demande_id' => $type->id,
        'valeurs' => [
            ['champ_demande_id' => $champ->id, 'valeur' => 'Motif de test'],
        ],
    ])->json('data');

    $response = $this->postJson("/api/demandes/{$demande['id']}/submit");

    $response->assertOk();
    $response->assertJsonPath('data.statut', StatutDemande::Soumise->value);
});

it('empêche l\'auteur de modifier une demande déjà soumise', function () {
    $employe = creerUtilisateur('employe');
    $type = creerTypeDemandeAvecChampObligatoire();
    $champ = $type->champs()->first();

    Sanctum::actingAs($employe);

    $demande = $this->postJson('/api/demandes', [
        'type_demande_id' => $type->id,
        'valeurs' => [['champ_demande_id' => $champ->id, 'valeur' => 'Motif']],
    ])->json('data');

    $this->postJson("/api/demandes/{$demande['id']}/submit")->assertOk();

    $response = $this->putJson("/api/demandes/{$demande['id']}", [
        'commentaire' => 'Tentative de modification',
    ]);

    $response->assertForbidden();
});

it('empêche un employé de voir la demande d\'un collègue', function () {
    $employeA = creerUtilisateur('employe');
    $employeB = creerUtilisateur('employe');
    $type = creerTypeDemandeAvecChampObligatoire();

    Sanctum::actingAs($employeA);
    $demande = $this->postJson('/api/demandes', ['type_demande_id' => $type->id])->json('data');

    Sanctum::actingAs($employeB);
    $response = $this->getJson("/api/demandes/{$demande['id']}");

    $response->assertForbidden();
});

it('permet le cycle complet : affectation puis validation approuvée', function () {
    $employe = creerUtilisateur('employe');
    $agent = creerUtilisateur('agent');
    $validateur = creerUtilisateur('validateur');
    $type = creerTypeDemandeAvecChampObligatoire();
    $champ = $type->champs()->first();

    Sanctum::actingAs($employe);
    $demande = $this->postJson('/api/demandes', [
        'type_demande_id' => $type->id,
        'valeurs' => [['champ_demande_id' => $champ->id, 'valeur' => 'Motif']],
    ])->json('data');
    $this->postJson("/api/demandes/{$demande['id']}/submit")->assertOk();

    Sanctum::actingAs($agent);
    $this->postJson("/api/demandes/{$demande['id']}/affectations", [
        'agent_id' => $agent->id,
    ])->assertCreated();

    $this->postJson("/api/demandes/{$demande['id']}/transmettre-validation")->assertOk();

    Sanctum::actingAs($validateur);
    $response = $this->postJson("/api/demandes/{$demande['id']}/validations", [
        'decision' => 'approuve',
    ]);

    $response->assertCreated();

    $this->assertDatabaseHas('demandes', [
        'id' => $demande['id'],
        'statut' => StatutDemande::Approuvee->value,
    ]);

    $this->assertDatabaseHas('validations', [
        'demande_id' => $demande['id'],
        'validateur_id' => $validateur->id,
        'decision' => 'approuve',
    ]);
});

it('refuse la validation par un employé sans rôle validateur/admin', function () {
    $employe = creerUtilisateur('employe');
    $agent = creerUtilisateur('agent');
    $type = creerTypeDemandeAvecChampObligatoire();
    $champ = $type->champs()->first();

    Sanctum::actingAs($employe);
    $demande = $this->postJson('/api/demandes', [
        'type_demande_id' => $type->id,
        'valeurs' => [['champ_demande_id' => $champ->id, 'valeur' => 'Motif']],
    ])->json('data');
    $this->postJson("/api/demandes/{$demande['id']}/submit")->assertOk();

    Sanctum::actingAs($agent);
    $this->postJson("/api/demandes/{$demande['id']}/affectations", ['agent_id' => $agent->id])->assertCreated();
    $this->postJson("/api/demandes/{$demande['id']}/transmettre-validation")->assertOk();

    // Un employé (même auteur) ne peut pas se prononcer sur sa propre demande.
    Sanctum::actingAs($employe);
    $response = $this->postJson("/api/demandes/{$demande['id']}/validations", [
        'decision' => 'approuve',
    ]);

    $response->assertForbidden();
});
