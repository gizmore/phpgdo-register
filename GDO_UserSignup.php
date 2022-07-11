<?php
namespace GDO\Register;

use GDO\Core\GDO;
use GDO\Net\GDT_IP;
use GDO\Core\GDT_CreatedAt;
use GDO\User\GDO_User;
use GDO\Core\GDT_CreatedBy;

/**
 * Record succesful signups.
 * 
 * @author gizmore
 * @since 7.0.1
 */
final class GDO_UserSignup extends GDO
{
	public function gdoCached() : bool { return false; }
	
	public function gdoColumns(): array
	{
		return [
			GDT_CreatedBy::make('us_creator'),
			GDT_CreatedAt::make('us_created'),
			GDT_IP::make('us_ip'),
		];
	}
	
	public static function onSignup(GDO_User $user) : self
	{
		return self::blank()->insert();
	}

}
