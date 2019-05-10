<?php

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */


/**
 * Class ilMoodReminderCron
 *
 * @author      Björn Heyser <info@bjoernheyser.de>
 *
 * @package     Plugins/MoodReminder
 */
class ilMoodReminderCron extends ilCronJob
{
	/**
	 * @var ilMoodReminderPlugin
	 */
	protected $plugin;
	
	/**
	 * @var integer
	 */
	protected $nowTimstamp;
	
	/**
	 * ilMoodReminderCron constructor.
	 */
	public function __construct()
	{
		$this->nowTimstamp = time();
	}
	
	/**
	 * @return ilMoodReminderPlugin
	 */
	public function getPlugin()
	{
		return $this->plugin;
	}
	
	/**
	 * @param ilMoodReminderPlugin $plugin
	 */
	public function setPlugin($plugin)
	{
		$this->plugin = $plugin;
	}
	
	public function getId()
	{
		return 'mood_barometer_reminder';
	}
	
	public function getTitle()
	{
		return $this->getPlugin()->txt("cron_title");
	}
	
	public function getDescription()
	{
		return $this->getPlugin()->txt("cron_info");
	}
	
	public function hasAutoActivation()
	{
		return false;
	}
	
	public function hasFlexibleSchedule()
	{
		return false;
	}
	
	public function getDefaultScheduleType()
	{
		return self::SCHEDULE_TYPE_DAILY;
	}
	
	function getDefaultScheduleValue()
	{
		return null;
	}
	
	public function run()
	{
		$result = new ilCronJobResult();

		if( !$this->getPlugin()->checkMoodBarometerPluginAvailable() )
		{
			$result->setMessage($this->getPlugin()->txt('check_mood_barometer_plugin'));
			$result->setStatus(ilCronJobResult::STATUS_INVALID_CONFIGURATION);
			return $result;
		}

		if( !$this->isFriday() )
		{
			$result->setMessage($this->getPlugin()->txt('result_other_day_than_friday'));
			$result->setStatus(ilCronJobResult::STATUS_NO_ACTION);
			return $result;
		}
		
		$this->getPlugin()->getMoodBarometerPlugin()->includePluginClasses();
		
		$userIds = $this->getUsersMissingMoodRecord(
			$this->getCurrentYear(), $this->getCurrentWeek()
		);
		
		if( !count($userIds) )
		{
			$result->setMessage($this->getPlugin()->txt('result_no_missing_mood_recs'));
			$result->setStatus(ilCronJobResult::STATUS_NO_ACTION);
			return $result;
		}
		
		$notification = new ilMoodReminderNotification($this->getPlugin());
		$notification->setRecipients($userIds);
		$notification->send();
		
		$result->setMessage(sprintf(
			$this->getPlugin()->txt('result_missing_mood_recs_notified'), count($userIds))
		);
		
		$result->setStatus(ilCronJobResult::STATUS_OK);
		
		return $result;
	}
	
	/**
	 * @param int $year
	 * @param int $week
	 * @return array
	 */
	protected function getUsersMissingMoodRecord($year, $week)
	{
		$roles = $this->getPlugin()->getMoodBarometerPlugin()->getConfig()->getDepartmentRoleIds();
		
		$allUserIds = $this->getAllUsersForRoles($roles);
		
		$notifyUserIds = ilMoodRepository::fetchUserIdsMissingMoodRecord($allUserIds, $year, $week);
		
		return $notifyUserIds;
	}
	
	/**
	 * @param array $roles
	 * @return array
	 */
	protected function getAllUsersForRoles($roles)
	{
		global $DIC; /* @var ILIAS\DI\Container $DIC */
		
		$userIds = array();
		
		foreach($roles as $roleId)
		{
			$userIds = array_merge($userIds, array_values(
				$DIC->rbac()->review()->assignedUsers($roleId)
			));
		}
		
		return array_unique($userIds);
	}
	
	/**
	 * @return int
	 */
	public function getCurrentYear()
	{
		return (int)date('Y', $this->nowTimstamp);
	}
	
	/**
	 * @return int
	 */
	public function getCurrentWeek()
	{
		return (int)date('W', $this->nowTimstamp);
	}
	
	/**
	 * @return bool
	 */
	public function isFriday()
	{
		return date('w', $this->nowTimstamp) == 5;
	}
}
