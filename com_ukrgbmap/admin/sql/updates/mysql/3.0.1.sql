ALTER TABLE `#__ukrgb_maps` ADD `params` VARCHAR(1024) NOT NULL DEFAULT '';
ALTER TABLE `#__ukrgb_maps` ADD `catid` int(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__ukrgb_maps` ADD COLUMN `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;

