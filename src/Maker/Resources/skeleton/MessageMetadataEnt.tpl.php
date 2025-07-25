<?= "<?php\n" ?>
namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ORM\Entity]
class <?= $class_name ?> extends BaseMessageMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options:["unsigned" => true])]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: FOSChatMessage::class, inversedBy: 'metadata')]
    protected MessageInterface $message;

    #[ORM\ManyToOne(targetEntity: <?= $participantEntClassName ?>::class)]
    protected ParticipantInterface $participant;
}