
update `#__ukrgb_map_point` p
INNER JOIN  `#__ukrgb_maps` m on p.riverguide = m.articleid
set p.mapid = m.id where p.mapid = 0;