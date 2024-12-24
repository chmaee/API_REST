<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UtilisateurRepository;
use App\State\DesinscriptionProcessor;
use App\State\InscriptionProcessor;
use App\State\UtilisateurDeleteProcessor;
use App\State\UtilisateurProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
#[UniqueEntity('login', message : "Ce login est déjà pris!")]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['adresseEmail'])]
#[UniqueEntity('adresseEmail', message : "Cet Email est déjà pris!")]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['utilisateur:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['evenement:read']],
            security: "is_granted('PUBLIC_ACCESS')",
            securityMessage: "Vous n'avez pas les droits nécessaires pour consulter tous les utilisateurs."
        ),
        new Post(
            processor: UtilisateurProcessor::class,
            denormalizationContext: ['groups' => ['utilisateur:create']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object == user",
            securityMessage: "Vous ne pouvez supprimer que votre propre compte.",
            processor: UtilisateurDeleteProcessor::class
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or object == user",
            securityMessage: "Vous ne pouvez modifier que votre propre compte.",
            processor: UtilisateurProcessor::class
        ),
        new Post(
            uriTemplate: '/evenements/{id}/inscription',
            uriVariables: [
                'id' => 'id'
            ],
            security: "is_granted('ROLE_USER')",
            processor: InscriptionProcessor::class
        ),

        new Delete(
            uriTemplate: '/evenements/{id}/desinscription',
            uriVariables: [
                'id' => 'id'
            ],
            security: "is_granted('ROLE_USER')",
            processor: DesinscriptionProcessor::class
        )


    ],
    normalizationContext: ['groups' => ['utilisateur:read']],
    denormalizationContext: ['groups' => ['utilisateur:create', 'utilisateur:update']]
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotNull(message: 'Le login ne peut pas être nul.', groups: ['utilisateur:create'])]
    #[Assert\NotBlank(message: 'Le login ne peut pas être vide.', groups: ['utilisateur:create'])]
    #[Assert\Length(
        min: 4,
        max: 20,
        minMessage: 'Il faut au moins 4 caractères!',
        maxMessage: 'Il faut au maximum 20 caractères!'
    )]
    #[Groups(['utilisateur:read', 'utilisateur:create','evenement:read','utilisateur:update'])]
    private ?string $login = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['evenement:read', 'participants:read', 'utilisateur:create','utilisateur:update', 'utilisateur:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 50)]
    #[Groups(['evenement:read', 'participants:read', 'utilisateur:create','utilisateur:update','utilisateur:read'])]
    private ?string $prenom = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['utilisateur:read'])]
    #[ApiProperty(writable: false)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $password = null;

    #[ApiProperty(readable: false)]
    #[Assert\NotBlank(message: 'Le mot de passe ne peut pas être vide.', groups: ['utilisateur:create'])]
    #[Assert\NotNull(message: 'Le mot de passe ne peut pas être nul.', groups: ['utilisateur:create'])]
    #[Assert\Length(
        min: 8,
        max: 30,
        minMessage: 'Le mot de passe doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le mot de passe ne peut pas dépasser {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,30}$#',
        message: 'Le mot de passe doit contenir au moins une minuscule, une majuscule et un chiffre.'
    )]
    #[Groups(['utilisateur:create', 'utilisateur:update'])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotNull(message: 'L\'adresse email ne peut pas être nulle.', groups: ['utilisateur:create'])]
    #[Assert\NotBlank(message: 'L\'adresse email ne peut pas être vide.', groups: ['utilisateur:create'])]
    #[Assert\Email(message: 'Veuillez entrer une adresse email valide !')]
    #[Groups(['utilisateur:read', 'utilisateur:create', 'utilisateur:update'])]
    private ?string $adresseEmail = null;

    /**
     * @var Collection<int, Evenement>
     */
    #[ORM\OneToMany(targetEntity: Evenement::class, mappedBy: 'organisateur', orphanRemoval: true)]
    #[Groups(['utilisateur:read'])]
    private Collection $evenements;

    #[ORM\OneToMany(mappedBy: 'utilisateur', targetEntity: Participation::class, cascade: ['persist', 'remove'])]
    private Collection $participations;

    public function __construct()
    {
        $this->evenements = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getAdresseEmail(): ?string
    {
        return $this->adresseEmail;
    }

    public function setAdresseEmail(string $adresseEmail): static
    {
        $this->adresseEmail = $adresseEmail;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return Collection<int, Evenement>
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): static
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): static
    {
        if ($this->evenements->removeElement($evenement)) {
            if ($evenement->getOrganisateur() === $this) {
                $evenement->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function conflitEvenement(Evenement $evenement): bool //Utiliser pour inscrire un event
    {
        foreach ($this->evenements as $inscription) {
            if ($inscription->getId() !== $evenement->getId() &&
                $evenement->getDateDebut() < $inscription->getDateFin() &&
                $evenement->getDateFin() > $inscription->getDateDebut()) {
                return true;
            }
        }
        return false;
    }
    #[Groups(['utilisateur:simple_evenements', 'utilisateur:read'])]
    public function getSimpleEvenementsInscrits(): array
    {
        return $this->participations->map(function (Participation $participation) {
            $evenement = $participation->getEvenement();
            return [
                'id' => $evenement->getId(),
                'nom' => $evenement->getNom(),
                'description' => $evenement->getDescription(),
                'date_debut' => $evenement->getDateDebut()->format('Y-m-d H:i:s'),
                'date_fin' => $evenement->getDateFin()->format('Y-m-d H:i:s'),
                'lieu' => $evenement->getLieu(),
                'categorie' => $evenement->getCategorie(),
            ];//
        })->toArray();
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        if (!$this->participations->contains($participation)) {
            $this->participations->add($participation);
            $participation->setUtilisateur($this);
        }
        return $this;
    }

    public function removeParticipation(Participation $participation): self
    {
        if ($this->participations->removeElement($participation)) {
            if ($participation->getUtilisateur() === $this) {
                $participation->setUtilisateur(null);
            }
        }
        return $this;
    }
}