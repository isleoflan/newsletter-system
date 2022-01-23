<?php

namespace IOL\Newsletter\v1\Content;

use IOL\Newsletter\v1\DataSource\Queue;
use IOL\Newsletter\v1\DataType\Email;
use IOL\Newsletter\v1\Enums\QueueType;
use IOL\Newsletter\v1\Exceptions\NotFoundException;
use IOL\Newsletter\v1\DataSource\Database;
use IOL\Newsletter\v1\DataType\Date;
use JetBrains\PhpStorm\Pure;

class Newsletter
{
    public const DB_TABLE = 'newsletter';

    private int $id;
    private string $title;
    private string $subject;
    private string $preheader;
    private ?Date $sendAt;

    private array $content = [];

    /**
     * @throws NotFoundException
     */
    public function __construct(?int $id = null)
    {
        if (!is_null($id)) {
            $this->loadData(Database::getRow('id', $id, self::DB_TABLE));
        }
    }

    /**
     * @throws NotFoundException
     */
    public function loadData(array|false $values): void
    {

        if (!$values || count($values) === 0) {
            throw new NotFoundException('Newsletter could not be loaded');
        }

        $this->id = $values['id'];
        $this->title = $values['title'];
        $this->subject = $values['subject'];
        $this->preheader = $values['preheader'];
        $this->sendAt = is_null($values['send_at']) ? null : new Date($values['send_at']);

        $this->loadContent();
    }

    public function addContent(NewsletterContent $content): void
    {
        $this->content[] = $content;
    }

    /**
     * @throws NotFoundException
     */
    public function loadContent(): void
    {
        $database = Database::getInstance();
        $database->where('newsletter_id', $this->id);
        $database->orderBy('sort', 'ASC');
        foreach($database->get(NewsletterContent::DB_TABLE) as $contentData){
            $content = new NewsletterContent();
            $content->loadData($contentData);
            $this->addContent($content);
        }
    }

    #[Pure]
    public function renderContent(): string
    {
        $return = '';
        /** @var NewsletterContent $content */
        foreach($this->content as $content){
            $return .= $content->render();
        }
        return $return;
    }

    public function send(string $name, Email $email): void
    {

        $mail = new Mail();
        $mail->setTemplate('newsletter');
        $mail->setReceiver($email);
        $mail->setSubject($this->subject);
        $mail->addVariable('preheader', $this->preheader);
        $mail->addVariable('content', $this->renderContent());
        $mail->addVariable('name', ' '.$name);

        $mailerQueue = new Queue(new QueueType(QueueType::MAILER));
        $mailerQueue->publishMessage(json_encode($mail), new QueueType(QueueType::MAILER));
    }
}