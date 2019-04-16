<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodReminderConfigGUI
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodReminder
 */
class ilMoodReminderConfigGUI extends ilPluginConfigGUI
{
	const CONFIG_TAB_ID = 'config_tab';
	
	const CMD_SHOW_CONFIG_FORM = 'showConfigForm';
	const CMD_SAVE_CONFIG_FORM = 'saveConfigForm';
	
	/**
	 * @var ilMoodReminderPlugin
	 */
	public $plugin_object;
	
	function performCommand($cmd)
	{
		switch($cmd)
		{
			case self::CMD_SAVE_CONFIG_FORM:
				
				$this->saveConfigForm();
				break;
				
			case self::CMD_SHOW_CONFIG_FORM:
			default:
				
				$this->showConfigForm();
				break;
		}
	}
	
	/**
	 * @return ilPropertyFormGUI
	 */
	protected function buildConfigForm()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$form = new ilPropertyFormGUI();
		
		$form->setFormAction($DIC->ctrl()->getFormAction($this, self::CMD_SHOW_CONFIG_FORM));
		$form->addCommandButton(self::CMD_SAVE_CONFIG_FORM, $DIC->language()->txt('save'));
		
		$form->setTitle($this->plugin_object->txt(self::CONFIG_TAB_ID));
		
		$salNeutralInp = new ilTextInputGUI(
			$this->plugin_object->txt('salutation_neutral_input'), 'salutation_neutral'
		);
		$salNeutralInp->setInfo(sprintf($this->plugin_object->txt('salutation_neutral_input_info'),
			implode(', ', ilMoodReminderNotification::getSalutationPlaceholders())
		));
		$salNeutralInp->setValue($this->plugin_object->getConfig()->getSalutationNeutral());
		$salNeutralInp->setRequired(true);
		$form->addItem($salNeutralInp);
		
		$salFemaleInp = new ilTextInputGUI(
			$this->plugin_object->txt('salutation_female_input'), 'salutation_female'
		);
		$salFemaleInp->setInfo(sprintf($this->plugin_object->txt('salutation_female_input_info'),
			implode(', ', ilMoodReminderNotification::getSalutationPlaceholders())
		));
		$salFemaleInp->setValue($this->plugin_object->getConfig()->getSalutationNeutral());
		$salFemaleInp->setRequired(true);
		$form->addItem($salFemaleInp);
		
		$salMaleInp = new ilTextInputGUI(
			$this->plugin_object->txt('salutation_male_input'), 'salutation_male'
		);
		$salMaleInp->setInfo(sprintf($this->plugin_object->txt('salutation_male_input_info'),
			implode(', ', ilMoodReminderNotification::getSalutationPlaceholders())
		));
		$salMaleInp->setValue($this->plugin_object->getConfig()->getSalutationNeutral());
		$salMaleInp->setRequired(true);
		$form->addItem($salMaleInp);
		
		$remMailSubjectInp = new ilTextInputGUI(
			$this->plugin_object->txt('reminder_subject_input'), 'reminder_subject'
		);
		$remMailSubjectInp->setInfo($this->plugin_object->txt('reminder_subject_input_info'));
		$remMailSubjectInp->setValue($this->plugin_object->getConfig()->getReminderSubject());
		$remMailSubjectInp->setRequired(true);
		$form->addItem($remMailSubjectInp);
		
		$remMailTextInp = new ilTextAreaInputGUI(
			$this->plugin_object->txt('reminder_text_input'), 'reminder_text'
		);
		$remMailTextInp->setInfo($this->plugin_object->txt('reminder_text_input_info'));
		$remMailTextInp->setValue($this->plugin_object->getConfig()->getReminderText());
		$remMailTextInp->setRequired(true);
		$remMailTextInp->setRows(10);
		$form->addItem($remMailTextInp);
		
		return $form;
	}
	
	protected function showConfigForm(ilPropertyFormGUI $form = null)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		if($form === null)
		{
			$form = $this->buildConfigForm();
		}
		
		$DIC->ui()->mainTemplate()->setContent($form->getHTML());
	}
	
	protected function saveConfigForm()
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$form = $this->buildConfigForm();
		
		$form->setValuesByPost();
		
		if( !$form->checkInput() )
		{
			return $this->showConfigForm($form);
		}
		
		$this->plugin_object->getConfig()->setSalutationNeutral(
			$form->getItemByPostVar('salutation_neutral')->getValue()
		);
		
		$this->plugin_object->getConfig()->setSalutationFemale(
			$form->getItemByPostVar('salutation_female')->getValue()
		);
		
		$this->plugin_object->getConfig()->setSalutationMale(
			$form->getItemByPostVar('salutation_male')->getValue()
		);
		
		$this->plugin_object->getConfig()->setReminderSubject(
			$form->getItemByPostVar('reminder_subject')->getValue()
		);
		
		$this->plugin_object->getConfig()->setReminderText(
			$form->getItemByPostVar('reminder_text')->getValue()
		);
		
		ilUtil::sendSuccess($this->plugin_object->txt('config_modified'), true);
		$DIC->ctrl()->redirect($this, self::CMD_SHOW_CONFIG_FORM);
	}
}
