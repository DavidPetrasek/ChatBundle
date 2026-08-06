<?= "<?php\n" ?>
namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ODM\Document]
class <?= $class_name ?> extends BaseThread
{
    #[ODM\Id]
    protected string|int|null $id = null;

    #[ODM\ReferenceMany(targetDocument: FOSChatMessage::class)]
    protected Collection $messages;

    #[ODM\EmbedMany(targetDocument: FOSChatThreadMetadata::class)]
    protected Collection $metadata;

    #[ODM\ReferenceMany(targetDocument: <?= $participantEntClassName ?>::class)]
    protected Collection $participants;

    #[ODM\ReferenceOne(targetDocument: <?= $participantEntClassName ?>::class)]
    protected ?ParticipantInterface $createdBy = null;
}