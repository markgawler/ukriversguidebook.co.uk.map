DROP TABLE IF EXISTS `#__ukrgb_maps`;
CREATE TABLE `#__ukrgb_maps` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`asset_id` INT(10)     NOT NULL DEFAULT '0',
`articleid` INT(11),
`sw_corner` POINT,
`ne_corner` POINT,
`map_type` INT( 11 ),
`published` tinyint(4) NOT NULL DEFAULT '0',
`catid`	   int(11)    NOT NULL DEFAULT '0',
`params`   VARCHAR(1024) NOT NULL DEFAULT ''

) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__ukrgb_map_point`;
CREATE TABLE `#__ukrgb_map_point` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`riverguide` INT(11),  /* Id of the river guide */
`point` POINT NOT NULL,
`type` TINYINT NOT NULL,  /* 1 = putin, 2 = takeout, 3 = alternate */
`description` varchar(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

