<?php
namespace GDO\Register\Test;

use GDO\Core\Module_Core;
use GDO\Register\Method\Form;
use GDO\Register\Method\Guest;
use GDO\Register\Module_Register;
use GDO\Tests\GDT_MethodTest;
use GDO\Tests\TestCase;
use GDO\User\GDO_User;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

final class RegisterTest extends TestCase
{

	public function testModuleVarHandling(): void
	{
		$module = Module_Register::instance();
		$module->saveConfigValue('signup_password_retype', false);
		self::assertTrue($module->cfgPasswordRetype() === false, 'Test if module vars are cached correctly upon change.');
	}

	public function testSuccess()
	{
		# register only works as ghost
		$this->userGhost();

		# Config for easy registration
		$module = Module_Register::instance();
		$module->saveConfigValue('signup_password_retype', false);
		$module->saveConfigValue('email_activation', false);
		$module->saveConfigValue('admin_activation', false);
		$module->saveConfigValue('activation_login', false);
		$module->saveConfigValue('force_tos', false);
		$method = Form::make();
		$parameters = [
			'user_name' => 'Peter3',
			'user_password' => '11111111',
		];
		$m = GDT_MethodTest::make()->method($method)->inputs($parameters);
		$m->execute('submit');
		$this->assert200('Check if registration works');
		assertNotEmpty(GDO_User::getByName('Peter3'), 'Check if new user Peter1 can sign up.');
	}

	public function testGuest()
	{
		# Another attempt which will not work.
		$this->userGhost();

		$method = Guest::make();
		$parameters = ['user_guest_name' => 'Casper'];
		$m = GDT_MethodTest::make()->method($method)->inputs($parameters);
		$m->execute('submit');
		if (!Module_Register::instance()->cfgGuestSignup())
		{
			$this->assert403('Check if guests cannot signup');
		}
		else
		{
			$this->assert200('Check if guest registration works.');

			$user = GDO_User::current();
			assertEquals('Casper', $user->getGuestName(), 'Check if guest register was success.');

			$user = Module_Core::instance()->cfgSystemUser();
			assertEquals('system', $user->getType(), 'Check if system user is still there.');
		}
	}

	public function testTOSFailed()
	{
		# Another attempt which will not work.
		$this->userGhost();

		$module = Module_Register::instance();
		$module->saveConfigValue('force_tos', true);
		$method = Form::make();
		$parameters = [
			'user_name' => 'Peter2',
			'user_password' => '11111111',
			'tos' => '2', # 2 is undetermined.
			'submit' => 1,
		];
		GDT_MethodTest::make()->method($method)->inputs($parameters)->execute();
		assertTrue($method->getForm()->getField('tos')->hasError(), 'Check if ToS checkbox prevents signup.');
	}

}
