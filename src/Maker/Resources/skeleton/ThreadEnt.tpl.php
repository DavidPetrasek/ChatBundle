<?= "<?php\n" ?>
namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ORM\Entity]
class <?= $class_name ?> extends BaseThread
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: <?= $participantEntClassName ?>::class)]
    protected ?ParticipantInterface $createdBy = null;

    /**
     * @var Collection<int, FOSChatMessage>
     */
    #[ORM\OneToMany(targetEntity: FOSChatMessage::class, mappedBy: 'thread')]
    protected Collection $messages;

    /**
     * @var Collection<int, FOSChatThreadMetadata>
     */
    #[ORM\OneToMany(targetEntity: FOSChatThreadMetadata::class, mappedBy: 'thread', cascade: ['all'])]
    protected Collection $metadata;

    public function __construct()
    {
        parent::__construct();
        $this->messages = new ArrayCollection();
        $this->metadata = new ArrayCollection();
    }
}