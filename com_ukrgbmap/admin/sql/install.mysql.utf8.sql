DROP TABLE IF EXISTS `#__ukrgb_maps`;
CREATE TABLE `#__ukrgb_maps` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`articleid` INT(11),
`sw_corner` POINT NOT NULL,
`ne_corner` POINT NOT NULL,
`map_type` INT( 11 )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `#__ukrgb_map_point`;
CREATE TABLE `#__ukrgb_map_point` (
`id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`riverguide` INT(11),  /* Id of the river guide */
`point` POINT NOT NULL,
`type` TINYINT NOT NULL,  /* 1 = putin, 2 = takeout, 3 = alternate */
`description` varchar(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

