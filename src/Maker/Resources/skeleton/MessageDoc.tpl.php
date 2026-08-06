<?= "<?php\n" ?>
namespace <?= $namespace ?>;

<?= $use_statements ?>

#[ODM\Document]
class <?= $class_name ?> extends BaseMessage
{
    #[ODM\Id]
    protected string|int|null $id = null;

    #[ODM\EmbedMany(targetDocument: FOSChatMessageMetadata::class)]
    protected Collection $metadata;

    #[ODM\ReferenceOne(targetDocument: FOSChatThread::class)]
    protected ThreadInterface $thread;

    #[ODM\ReferenceOne(targetDocument: <?= $participantEntClassName ?>::class)]
    protected ParticipantInterface $sender;
}