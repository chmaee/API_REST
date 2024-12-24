<?php
namespace App\DataProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Evenement;
use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;

class UtilisateurEvenementDataProvider implements ProviderInterface
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $userId = $uriVariables['id'] ?? null;

        if (!$userId) {
            throw new \InvalidArgumentException("ID de l'utilisateur manquant.");
        }

        $user = $this->managerRegistry->getRepository(Utilisateur::class)->find($userId);

        if (!$user) {
            throw new \Exception("Utilisateur non trouvé.");
        }

        $participations = $user->getParticipations();
        $events = [];

        foreach ($participations as $participation) {
            $events[] = $participation->getEvenement();
        }

        return $events;
    }
}
