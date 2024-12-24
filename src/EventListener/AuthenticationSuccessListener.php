<?php
namespace App\EventListener;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class AuthenticationSuccessListener
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager
    )
    {}

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        // Ajout des informations utilisateur dans la réponse
        $data['id'] = $user->getId();
        $data['login'] = $user->getLogin();
        $data['nom'] = $user->getNom();
        $data['prenom'] = $user->getPrenom();
        $data['email'] = $user->getAdresseEmail();
        $data['roles'] = $user->getRoles();

        $jwt = $this->jwtManager->parse($data['token']);
        $data['token_exp'] = $jwt['exp'];

        $event->setData($data);
    }
}