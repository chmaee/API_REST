<?php
namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Evenement;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EvenementProcessor implements ProcessorInterface
{
    private Security $security;
    private ProcessorInterface $persistProcessor;

    public function __construct(
        Security $security,
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        ProcessorInterface $persistProcessor
    ) {
        $this->security = $security;
        $this->persistProcessor = $persistProcessor;
    }

    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): mixed {
        if (!$data instanceof Evenement) {
            throw new \RuntimeException('Expected Evenement entity.');
        }

        $user = $this->security->getUser();

        // Vérification si l'utilisateur est authentifié
        if (null === $user) {
            throw new \RuntimeException('Aucun utilisateur connecté.');
        }

        // Vérification des rôles
        if (!$this->security->isGranted('ROLE_ORGANISATEUR')) {
            throw new \RuntimeException('Vous devez être connecté en tant qu\'organisateur pour créer un événement.');
        }

        // Affecter l'utilisateur courant comme organisateur
        $data->setOrganisateur($user);

        // Continuer le traitement normal
        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
