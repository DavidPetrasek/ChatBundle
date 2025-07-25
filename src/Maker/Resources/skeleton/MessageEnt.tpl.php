<?= "<?php\n" ?>
namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ORM\Entity]
class <?= $class_name ?> extends BaseMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: FOSChatThread::class, inversedBy: 'messages')]
    protected ThreadInterface $thread;

    #[ORM\ManyToOne(targetEntity: <?= $participantEntClassName ?>::class)]
    protected ParticipantInterface $sender;

    /**
     * @var Collection<int, FOSChatMessageMetadata>
     */
    #[ORM\OneToMany(targetEntity: FOSChatMessageMetadata::class, mappedBy: 'message', cascade: ['all'])]
    protected Collection $metadata;

    public function __construct()
    {
        parent::__construct();
        $this->metadata = new ArrayCollection();
    }
}