<?php
namespace App\Services;

use Kreait\Firebase\Factory;

class PromotionService
{
    protected $database;

    public function __construct()
    {
        $serviceAccountPath = base_path('config/firebase_credentials.json');

        $factory = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->withDatabaseUri('https://gestionapprenant-c42e2-default-rtdb.firebaseio.com');

        $this->database = $factory->createDatabase();
        $this->auth = $factory->createAuth();
        $this->storage = $factory->createStorage(); // Ajout de Firebase Storage

    }

    public function createPromotion(array $data)
    {
        // Validation des données
        $this->validatePromotionData($data);

        // Création de la promotion
        $promotionRef = $this->database->getReference('promotions')->push();
        $promotionKey = $promotionRef->getKey();

       


      

        return $promotionKey;
    }

    private function formatReferentiels(array $referentiels)
    {
        return array_map(function($referentiel) {
            return [
                'infos' => $referentiel['infos'] ?? '',
                'apprenants' => $this->formatApprenants($referentiel['apprenants'] ?? []),
            ];
        }, $referentiels);
    }

    private function formatApprenants(array $apprenants)
    {
        return array_map(function($apprenant) {
            return [
                'nom' => $apprenant['nom'] ?? '',
                'prenom' => $apprenant['prenom'] ?? '',
                'adresse' => $apprenant['adresse'] ?? '',
                'email' => $apprenant['email'] ?? '',
                'password' => $apprenant['password'] ?? '',
                'telephone' => $apprenant['telephone'] ?? '',
                'role' => $apprenant['role'] ?? '',
                'statut' => $apprenant['statut'] ?? '',
                'photo' => $apprenant['photo'] ?? '',
            ];
        }, $apprenants);
    }

    private function validatePromotionData(array $data)
    {
        // Implémentez vos règles de validation ici
        if (empty($data['libelle'])) {
            throw new \Exception('Libelle is required.');
        }

        if (empty($data['date_debut'])) {
            throw new \Exception('Date de début is required.');
        }
        
        // Autres validations peuvent être ajoutées ici
    }

    public function updatePromotion($id, array $data)
    {
        $reference = $this->database->getReference('promotions/' . $id);
        $snapshot = $reference->getSnapshot();

        if (!$snapshot->exists()) {
            throw new \Exception('Promotion not found.');
        }

        $reference->update($data);
    }

    public function listPromotions()
    {
        $reference = $this->database->getReference('promotions');
        $snapshot = $reference->getSnapshot();
        return $snapshot->getValue();
    }

    public function getPromotionById($id)
    {
        $reference = $this->database->getReference('promotions/' . $id);
        $snapshot = $reference->getSnapshot();

        if (!$snapshot->exists()) {
            throw new \Exception('Promotion not found.');
        }

        return $snapshot->getValue();
    }

    // Ajoutez les autres méthodes pour gérer les référentiels, stats, etc.

}
