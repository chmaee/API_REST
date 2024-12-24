<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\DataProvider\UtilisateurEvenementDataProvider;
use App\Repository\EvenementRepository;
use App\State\EvenementProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: EvenementRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['evenement:read']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['evenement:read']],
            security: "is_granted('PUBLIC_ACCESS')",
            securityMessage: "Vous n'avez pas les droits nécessaires pour consulter tous les événements."
        ),

        new GetCollection(
            uriTemplate: '/utilisateurs/{id}/evenements',
            normalizationContext: ['groups' => ['utilisateur:simple_evenements']],
            provider: UtilisateurEvenementDataProvider::class,
            security: "is_granted('ROLE_USER') and user.getId() == request.attributes.get('id')",
            securityMessage: "Vous n'avez pas les droits nécessaires pour consulter ces événements."
        ),

        new GetCollection(
            uriTemplate: '/organisateur/evenements',
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_ORGANISATEUR')",
            securityMessage: "Vous n'avez pas les droits nécessaires pour consulter ces événements.",
            normalizationContext: ['groups' => ['evenement:read']]
        ),
        new Post(
            security: "is_granted('ROLE_ORGANISATEUR')",
            securityMessage: "Seuls les organisateurs peuvent créer des événements.",
            processor: EvenementProcessor::class,
            denormalizationContext: ['groups' => ['evenement:create']]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or (is_granted('ROLE_ORGANISATEUR') and object.getOrganisateur() == user)",
            securityMessage: "Vous n'avez pas le droit de supprimer cet événement."
        ),
        new Patch(
            security: "is_granted('ROLE_ORGANISATEUR') and object.getOrganisateur() == user",
            denormalizationContext: ['groups' => ['evenement:update']]
        )
    ],

    normalizationContext: ['groups' => ['evenement:read']]
)]
class Evenement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['evenement:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['evenement:read', 'evenement:create', 'evenement:update', 'utilisateur:simple_evenements'])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['evenement:read', 'evenement:create', 'evenement:update', 'utilisateur:simple_evenements'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['evenement:read', 'evenement:create', 'evenement:update', 'utilisateur:simple_evenements'])]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['evenement:read', 'evenement:create', 'evenement:update', 'utilisateur:simple_evenements'])]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(length: 255)]
    #[Groups(['evenement:read', 'evenement:create', 'evenement:update', 'utilisateur:simple_evenements'])]
    private ?string $lieu = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['evenement:read', 'evenement:create', 'evenement:update'])]
    private ?int $capacite = null;

    #[ORM\Column]
    #[Groups(['evenement:create', 'evenement:update'])]
    private ?int $budget = null;

    #[ORM\Column(length: 30)]
    #[Groups(['evenement:read', 'evenement:create', 'evenement:update','utilisateur:simple_evenements'])]
    private ?string $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'evenements', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['evenement:read'])]
    #[ApiProperty(writable: false)]
    private ?Utilisateur $organisateur = null;

    #[ORM\Column]
    #[Groups(['evenement:read', 'evenement:create', 'evenement:update'])]
    private ?bool $participant_visibilite = true;

    #[ORM\OneToMany(mappedBy: 'evenement', targetEntity: Participation::class, cascade: ['persist', 'remove'])]
    private Collection $participations;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(?int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getBudget(): ?int
    {
        return $this->budget;
    }

    public function setBudget(int $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getOrganisateur(): ?Utilisateur
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?Utilisateur $organisateur): static
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getParticipations(?Utilisateur $user = null): Collection
    {
        if ($this->organisateur && $this->organisateur === $user) {
            return $this->participations;
        }

        if ($this->participant_visibilite) {
            return $this->participations;
        }

        return new ArrayCollection();
    }


    public function addParticipant(Utilisateur $utilisateur): static
    {
        if (!$this->participations->exists(function ($key, $participation) use ($utilisateur) {
            return $participation->getUtilisateur() === $utilisateur;
        })) {
            if ($this->capacite !== null && $this->participations->count() >= $this->capacite) {
                throw new \Exception('Capacité maximale atteinte pour cet événement');
            }

            $participation = new Participation();
            $participation->setUtilisateur($utilisateur);
            $participation->setEvenement($this);

            $this->participations->add($participation);
            $utilisateur->addParticipation($participation);
        }

        return $this;
    }

    public function removeParticipant(Utilisateur $participant): static
    {
        $participation = $this->participations->filter(function (Participation $participation) use ($participant) {
            return $participation->getUtilisateur() === $participant;
        })->first();

        if ($participation) {
            $this->participations->removeElement($participation);
            $participant->removeParticipation($participation);
        }

        return $this;
    }

    public function desinscrireParticipant(Utilisateur $utilisateur): bool
    {
        if (!$this->participations->exists(function ($key, $participation) use ($utilisateur) {
            return $participation->getUtilisateur() === $utilisateur;
        })) {
            return false;
        }

        $this->removeParticipant($utilisateur);
        return true;
    }
    public function isParticipantVisibilite(): ?bool
    {
        return $this->participant_visibilite;
    }

    public function setParticipantVisibilite(bool $participant_visibilite): static
    {
        $this->participant_visibilite = $participant_visibilite;
        return $this;
    }




}
