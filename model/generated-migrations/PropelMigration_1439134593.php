<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1439134593.
 * Generated on 2015-08-10 01:36:33
 */
class PropelMigration_1439134593
{
	public $comment = '';

	public function preUp($manager)
	{
		// add the pre-migration code here
	}

	public function postUp($manager)
	{
		// add the post-migration code here
	}

	public function preDown($manager)
	{
		// add the pre-migration code here
	}

	public function postDown($manager)
	{
		// add the post-migration code here
	}

	/**
	 * Get the SQL statements for the Up migration
	 *
	 * @return array list of the SQL strings to execute for the Up migration
	 *               the keys being the datasources
	 */
	public function getUpSQL()
	{
		return [
			'booking' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `resource`;

ALTER TABLE `booking` DROP FOREIGN KEY `bookingUserFK`;

ALTER TABLE `booking`

  CHANGE `time` `time` VARCHAR(255) NOT NULL,

  ADD `day` VARCHAR(255) NOT NULL AFTER `user_id`,

  DROP `court`;

ALTER TABLE `booking` ADD CONSTRAINT `bookingUserFK`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT;

ALTER TABLE `preference` DROP FOREIGN KEY `preferenceUserFK`;

ALTER TABLE `preference` ADD CONSTRAINT `preferenceUserFK`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
		];
	}

	/**
	 * Get the SQL statements for the Down migration
	 *
	 * @return array list of the SQL strings to execute for the Down migration
	 *               the keys being the datasources
	 */
	public function getDownSQL()
	{
		return [
			'booking' => '
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE `booking` DROP FOREIGN KEY `bookingUserFK`;

ALTER TABLE `booking`

  CHANGE `time` `time` DATETIME NOT NULL,

  ADD `court` VARCHAR(10) NOT NULL AFTER `time`,

  DROP `day`;

ALTER TABLE `booking` ADD CONSTRAINT `bookingUserFK`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE;

ALTER TABLE `preference` DROP FOREIGN KEY `preferenceUserFK`;

ALTER TABLE `preference` ADD CONSTRAINT `preferenceUserFK`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE;

CREATE TABLE `resource`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(50) NOT NULL,
    `value` VARCHAR(50) NOT NULL,
    `created` DATETIME DEFAULT \'0000-00-00 00:00:00\' NOT NULL,
    `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
		];
	}

}