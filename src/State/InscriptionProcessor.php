<?php
namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use App\Repository\EvenementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use ApiPlatform\Metadata\Operation;
use \App\Entity\Participation;

class InscriptionProcessor implements ProcessorInterface
{
    private Security $security;
    private EvenementRepository $evenementRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(Security $security, EvenementRepository $evenementRepository, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->evenementRepository = $evenementRepository;
        $this->entityManager = $entityManager;
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): mixed {
        $user = $this->security->getUser();

        if (null === $user) {
            throw new \RuntimeException('Aucun utilisateur connecté.');
        }

        $evenement = $this->evenementRepository->find($uriVariables['id']);
        if (!$evenement) {
            throw new \RuntimeException('Événement non trouvé.');
        }

        foreach ($evenement->getParticipations() as $participation) {
            if (!$participation instanceof Participation) {
                throw new \RuntimeException('Données de participation invalides.');
            }

            if ($participation->getUtilisateur() === $user) {
                throw new \RuntimeException('Vous êtes déjà inscrit à cet événement.');
            }
        }


        if ($user->conflitEvenement($evenement)) {
            throw new \RuntimeException('Inscription échouée, il y a un conflit d\'horaires avec un événement existant.');
        }

        $participation = new Participation();
        $participation->setUtilisateur($user);
        $participation->setEvenement($evenement);

        $this->entityManager->persist($participation);
        $this->entityManager->flush();

        return $evenement;
    }

}
