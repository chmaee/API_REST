<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Entity\Participation;
use App\Repository\EvenementRepository;
use App\Repository\ParticipationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Metadata\Operation;

class DesinscriptionProcessor implements ProcessorInterface
{
    private Security $security;
    private EvenementRepository $evenementRepository;
    private EntityManagerInterface $entityManager;
    private ParticipationRepository $participationRepository;

    public function __construct(Security $security, EvenementRepository $evenementRepository, EntityManagerInterface $entityManager, ParticipationRepository $participationRepository)
    {
        $this->security = $security;
        $this->evenementRepository = $evenementRepository;
        $this->entityManager = $entityManager;
        $this->participationRepository = $participationRepository;
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): mixed {
        $user = $this->security->getUser();
        $eventId = $data->getId();

        if (null === $user) {
            throw new \RuntimeException('Aucun utilisateur connecté.');
        }

        $evenement = $this->evenementRepository->find($uriVariables['id']);
        if (!$evenement) {
            throw new \RuntimeException('Événement non trouvé.');
        }

        $participation = $this->participationRepository->findOneBy([
            'utilisateur' => $user,
            'evenement' => $eventId
        ]);

        if (!$participation) {
            throw new \RuntimeException('Désinscription échouée, vous n\'êtes pas inscrit à cet événement.');
        }

        $evenement->desinscrireParticipant($user);

        $this->entityManager->remove($participation);
        $this->entityManager->flush();

        return $evenement;
    }
}
