<?php


namespace ADT;


use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\Extra\SpoofCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Nette\Forms\Container;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;


class EmailStrictInput extends TextInput
{
	const VALIDATE_EMAIL = [__CLASS__, 'validateEmail'];

	public static function addEmailStrict(Container $container, $name, $label = null, $errorMessage = 'Invalid email address.')
	{
		$component = (new TextInput($label))
			->setRequired(false)
			->setNullable(true);

		$component
			->addCondition(Form::FILLED)
			->addRule(Form::EMAIL, $errorMessage)
			->addRule(EmailStrictInput::VALIDATE_EMAIL, $errorMessage)
			->endCondition();

		$container->addComponent($component, $name);
		return $component;
	}

	public static function validateEmail(TextInput $control)
	{
		$validator = new EmailValidator();
		$multipleValidations = new MultipleValidationWithAnd([
			new NoRFCWarningsValidation(),
			new DNSCheckValidation(),
			new SpoofCheckValidation(),
		]);
		return $validator->isValid($control->getValue(), $multipleValidations);
	}

	public static function register()
	{
		Form::extensionMethod('addEmailStrict', [__CLASS__, 'addEmailStrict']);
		Container::extensionMethod('addEmailStrict', [__CLASS__, 'addEmailStrict']);
	}
}
