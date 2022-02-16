<?php

namespace IOL\Newsletter\v1\Content;

use IOL\Newsletter\v1\DataSource\Database;
use IOL\Newsletter\v1\DataType\Email;
use IOL\Newsletter\v1\Exceptions\NotFoundException;
use JetBrains\PhpStorm\Pure;

class NewsletterContent
{
    public const DB_TABLE = 'content';

    private int $id;
    private ?string $image;
    private ?string $title;
    private ?array $text;
    private ?string $buttonText;
    private ?string $buttonLink;
    private int $sort;

    /**
     * @throws NotFoundException
     */
    public function __construct(?int $id = null)
    {
        if (!is_null($id)) {
            $this->loadData(Database::getRow('id', $id, self::DB_TABLE));
        }
    }

    public function loadData(array|false $values)
    {

        if (!$values || count($values) === 0) {
            throw new NotFoundException('Content could not be loaded');
        }

        $this->id = $values['id'];
        $this->image = $values['image'];
        $this->title = $values['title'];
        $this->text = json_decode($values['text'], true);
        $this->buttonText = $values['button_text'];
        $this->buttonLink = $values['button_link'];
        $this->sort = $values['sort'];
    }

    #[Pure]
    public function render(): string
    {
        $return  = '<tr>';
        $return .= '<td class="content text-center text-wrap" style="font-family: Open Sans, -apple-system, BlinkMacSystemFont, Roboto, Helvetica Neue, Helvetica, Arial, sans-serif; padding: 40px 48px;" align="center">';
        $return .= is_null($this->image) ? '' : '<img src="[[/assets/'.$this->image.']]" class=" mb-md" width="300" alt="" style="line-height: 100%; border: 0 none; outline: none; text-decoration: none; vertical-align: baseline; font-size: 0; margin-top: 0; margin-bottom: 16px;" />';
        $return .= '<h3 style="font-weight: 300; font-size: 20px; line-height: 120%; margin: 0 0 .5em;">'.$this->title.'</h3>';
        foreach($this->text as $text) {
            $return .= '<p class="text-muted-dark" style="color: #728c96; margin: 0;">'.$text.'</p>';
        }
        $return .= '</td>';
        $return .= '</tr>';

        $return .= $this->renderButton();

        return $return;
    }

    public function renderButton(): string
    {
        if(!is_null($this->buttonText)){
            return '<tr><td class="content" style="font-family: Open Sans, -apple-system, BlinkMacSystemFont, Roboto, Helvetica Neue, Helvetica, Arial, sans-serif; padding: 40px 48px;">'.
            '<table cellspacing="0" cellpadding="0" style="font-family: Open Sans, -apple-system, BlinkMacSystemFont, Roboto, Helvetica Neue, Helvetica, Arial, sans-serif; border-collapse: collapse; width: 100%;">'.
            '<tr><td align="center" style="font-family: Open Sans, -apple-system, BlinkMacSystemFont, Roboto, Helvetica Neue, Helvetica, Arial, sans-serif;">'.
            '<table cellpadding="0" cellspacing="0" border="0" class="bg-green rounded" style="font-family: Open Sans, -apple-system, BlinkMacSystemFont, Roboto, Helvetica Neue, Helvetica, Arial, sans-serif; '.
            'border-collapse: separate; width: 100%; color: #ffffff; border-radius: 3px;" bgcolor="#5eba00">'.
            '<tr><td align="center" valign="top" class="lh-1" style="font-family: Open Sans, -apple-system, BlinkMacSystemFont, Roboto, Helvetica Neue, Helvetica, Arial, sans-serif; line-height: 100%;">'.
            '<a href="'.$this->buttonLink.'" class="btn bg-green border-green" style="color: #ffffff; padding: 12px 32px; border: 1px solid #5eba00; text-decoration: none; white-space: nowrap; '.
            'font-weight: 600; font-size: 16px; border-radius: 3px; line-height: 100%; display: block; -webkit-transition: .3s background-color; transition: .3s background-color; background-color: #5eba00;">'.
            '<span class="btn-span" style="color: #ffffff; font-size: 16px; text-decoration: none; white-space: nowrap; font-weight: 600; line-height: 100%;">'.
            $this->buttonText.
            '</span></a></td></tr></table></td></tr></table></td></tr>';
        }
        return '';
    }
}