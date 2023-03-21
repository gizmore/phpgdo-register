<?php
namespace GDO\Register\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDO;
use GDO\DB\Query;
use GDO\Register\GDO_UserActivation;
use GDO\Register\Module_Register;
use GDO\Table\MethodQueryTable;
use GDO\UI\GDT_Button;

/**
 * Open user activations for staff.
 *
 * @version 6.10
 * @since 6.06
 * @author gizmore
 */
final class Activations extends MethodQueryTable
{

	use MethodAdmin;

	public function onRenderTabs(): void
	{
		$this->renderAdminBar();
		Module_Register::instance()->renderAdminBar();
	}

	public function getTitleLangKey() { return 'link_activations'; }

	public function gdoTable(): GDO
	{
		return GDO_UserActivation::table();
	}

	public function getQuery(): Query
	{
		return GDO_UserActivation::table()->select();
	}

	public function gdoHeaders(): array
	{
		$gdo = $this->gdoTable();
		return [
			GDT_Button::make('btn_activate'),
			$gdo->gdoColumn('ua_time'),
			$gdo->gdoColumn('user_name'),
			$gdo->gdoColumn('user_register_ip'),
			$gdo->gdoColumn('user_email'),
			$gdo->gdoColumn('ua_email_confirmed'),
		];
	}


}
