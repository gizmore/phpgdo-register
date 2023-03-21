<?php
namespace GDO\Register;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_Serialize;
use GDO\Core\GDT_Token;
use GDO\Crypto\GDT_PasswordHash;
use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;
use GDO\Mail\GDT_Email;
use GDO\Net\GDT_IP;
use GDO\Net\GDT_Url;
use GDO\UI\GDT_Message;
use GDO\User\GDT_Username;

/**
 * User activation table.
 *
 * @version 7.0.1
 * @since 3.1.0
 * @author gizmore
 */
class GDO_UserActivation extends GDO
{

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_AutoInc::make('ua_id'),
			GDT_Token::make('ua_token')->notNull(),
			GDT_CreatedAt::make('ua_time')->notNull(),
			GDT_DeletedAt::make('ua_deleted'),
			GDT_Timestamp::make('ua_email_confirmed'),

			GDT_Message::make('ua_message'),

			# We copy these fields to user table
			GDT_Username::make('user_name')->notNull()->unique(false),
			GDT_PasswordHash::make('user_password'),
			GDT_Email::make('user_email'),
			GDT_IP::make('user_register_ip')->notNull(),

			GDT_Serialize::make('ua_data'),
		];
	}

	public function getIP() { return $this->gdoVar('user_register_ip'); }

	public function getEmail() { return $this->gdoVar('user_email'); }

	public function getUsername() { return $this->gdoVar('user_name'); }

	public function getMessage() { return $this->gdoVar('ua_message'); }

	public function isConfirmed() { return $this->gdoVar('ua_email_confirmed') !== null; }

	public function getUrl() { return GDT_Url::absolute($this->getHref()); }

	public function getHref() { return href('Register', 'Activate', "&id={$this->getID()}&token={$this->getToken()}&convert_guest=1"); }

	public function getID(): ?string { return $this->gdoVar('ua_id'); }

	public function getToken() { return $this->gdoVar('ua_token'); }

	public function isDeleted(): bool { return $this->gdoVar('ua_deleted') !== null; }

	public function href_btn_activate() { return href('Register', 'AdminActivate', '&id=' . $this->getID()); }

	public function renderUserName() { return $this->gdoVar('user_name'); }

	public function getPasswordHash(): string
	{
		return $this->gdoVar('user_password');
	}

// 	public function getPasswordHash() : string
// 	{
// 		$bcrypt = BCrypt::create($this->getPassword());
// 		return $bcrypt->__toString();
// 	}

	public function getActivateTime(): int
	{
		$created = $this->gdoVar('ua_time');
		return round(Time::getAge($created));
	}

}
