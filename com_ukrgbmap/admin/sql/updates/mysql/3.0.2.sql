ALTER TABLE `#__ukrgb_maps` ADD `published` tinyint(4) NOT NULL DEFAULT '0';
ALTER TABLE `#__ukrgb_maps`  modify sw_corner point null;
ALTER TABLE `#__ukrgb_maps`  modify ne_corner point null;
