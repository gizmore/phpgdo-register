<?php
namespace GDO\Register\Method;

use GDO\Admin\MethodAdmin;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\Core\Method;
use GDO\Register\Module_Register;

/**
 * Show a menu of admin options for the register module.
 *
 * @author gizmore
 */
final class Admin extends Method
{

	use MethodAdmin;

	public function getMethodTitle(): string
	{
		return t('perm_admin');
	}

	public function execute(): GDT
	{
		return GDT_Response::make();
	}

	public function onRenderTabs(): void
	{
		$this->renderAdminBar();
		Module_Register::instance()->renderAdminBar();
	}

}
