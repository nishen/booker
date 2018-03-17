<?php

/**
 * Data object containing the SQL and PHP code to migrate the database
 * up to version 1438183646.
 * Generated on 2015-07-30 01:27:26
 */
class PropelMigration_1438183646
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

CREATE INDEX `fi_kingUserFK` ON `booking` (`user_id`);

ALTER TABLE `booking` ADD CONSTRAINT `bookingUserFK`
    FOREIGN KEY (`user_id`)
    REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT;

CREATE INDEX `fi_ferenceUserFK` ON `preference` (`user_id`);

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

DROP INDEX `fi_kingUserFK` ON `booking`;

ALTER TABLE `preference` DROP FOREIGN KEY `preferenceUserFK`;

DROP INDEX `fi_ferenceUserFK` ON `preference`;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
',
		];
	}

}