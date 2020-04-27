<?php


namespace ADT;


use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\NoRFCWarningsValidation;
use Egulias\EmailValidator\Validation\SpoofCheckValidation;
use Nette\Forms\Container;
use Nette\Forms\Controls\TextInput;
use Nette\Forms\Form;


class EmailInput extends TextInput
{
	const VALIDATE_EMAIL = [__CLASS__, 'validateEmail'];

	public static function addValidatedEmail(Container $container, $name, $label, $errorMessage)
	{
		$component = (new TextInput($label))
			->setRequired(false)
			->addRule(Form::EMAIL, $errorMessage)
			->addRule(EmailInput::VALIDATE_EMAIL, $errorMessage);
		$container->addComponent($component, $name);
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
		Form::extensionMethod('addValidatedEmail', [__CLASS__, 'addValidatedEmail']);
		Container::extensionMethod('addValidatedEmail', [__CLASS__, 'addValidatedEmail']);
	}
}