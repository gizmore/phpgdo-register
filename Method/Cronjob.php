<?php
namespace GDO\Register\Method;

use GDO\Core\Application;
use GDO\Cronjob\MethodCronjob;
use GDO\Date\Time;
use GDO\DB\Database;
use GDO\Register\GDO_UserActivation;
use GDO\Register\Module_Register;

/**
 * Delete expired activation codes.
 *
 * @version 7.0.0
 * @since 6.2.0
 * @author gizmore
 */
final class Cronjob extends MethodCronjob
{

	public function run(): void
	{
		$module = Module_Register::instance();
		if (0 != ($timeout = $module->cfgEmailActivationTimeout()))
		{
			$cut = Time::getDate(Application::$TIME - $timeout);
			GDO_UserActivation::table()->deleteWhere("ua_time < '$cut'");
			if ($affected = Database::instance()->affectedRows())
			{
				$this->logNotice("Deleted $affected old user activations.");
			}
		}
	}

}
