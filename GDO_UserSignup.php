<?php
namespace GDO\Register;

use GDO\Core\GDO;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Net\GDT_IP;
use GDO\User\GDO_User;

/**
 * Record succesful signups.
 *
 * @since 7.0.1
 * @author gizmore
 */
final class GDO_UserSignup extends GDO
{

	public static function onSignup(GDO_User $user): self
	{
		return self::blank()->insert();
	}

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_CreatedBy::make('us_creator'),
			GDT_CreatedAt::make('us_created'),
			GDT_IP::make('us_ip'),
		];
	}

}
