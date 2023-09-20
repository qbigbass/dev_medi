<?php


namespace Bitrix\Calendar\ICal\MailInvitation;


use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ObjectException;
use Bitrix\Main\Text\Encoding;

class SenderCancelInvitation extends SenderInvitation
{
	public const METHOD = 'cancel';

	/**
	 * @return array[]
	 * @throws ObjectException
	 */
	protected function getContent(): array
	{
		$attachmentManager = AttachmentCancelManager::createInstance($this->event);
		$this->uid = $attachmentManager->getUid();

		return [[
			'CONTENT' => Encoding::convertEncoding($attachmentManager->getContent(), SITE_CHARSET, "utf-8"),
			'CONTENT_TYPE' => self::CONTENT_TYPE,
			'METHOD' => mb_strtolower(self::METHOD),
			'CHARSET' => self::CHARSET,
			'NAME' => self::ATTACHMENT_NAME,
			'ID' => Helper::getUniqId(),
		]];
	}

	/**
	 * @return array
	 * @throws ObjectException
	 */
	protected function getMailEventField(): array
	{
		return [
			"=Reply-To" => "{$this->context->getAddresser()->getFullName()} <{$this->context->getAddresser()->getEmail()}>",
			"=From" => "{$this->context->getAddresser()->getFullName()} <{$this->context->getAddresser()->getEmail()}>",
			"=Message-Id" => $this->getMessageId(),
			"=In-Reply-To" => $this->getMessageReplyTo(),
			'EMAIL_FROM' => $this->context->getAddresser()->getEmail(),
			'EMAIL_TO' => $this->context->getReceiver()->getEmail(),
			'MESSAGE_SUBJECT' => $this->getSubjectMessage(),
			'MESSAGE_PHP' => $this->getBodyMessage(),
			'DATE_FROM' => $this->getDateForTemplate(),
			'NAME' => $this->event['NAME'],
			'DESCRIPTION' => $this->event['DESCRIPTION'],
			'ATTENDEES' => $this->event['ICAL_ATTENDEES'],
			'ORGANIZER' => $this->context->getAddresser()->getFullNameWithEmail(),
			'LOCATION' => $this->event['TEXT_LOCATION'],
			'FILES_LINK' =>$this->getFilesLink(),
			'METHOD' => self::METHOD,
		];
	}

	/**
	 * @return string
	 */
	protected function getMessageReplyTo(): string
	{
		return $this->getMessageId();
	}

	/**
	 * @return string
	 */
	protected function getSubjectTitle(): string
	{
		return Loc::getMessage("EC_CALENDAR_ICAL_MAIL_METHOD_CANCEL") . ": {$this->event['NAME']}";
	}

	/**
	 *
	 */
	public function executeAfterSuccessfulInvitation()
	{
	}
}