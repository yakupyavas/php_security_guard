CREATE TABLE IF NOT EXISTS `controll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_adress` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `first_conn` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `last_conn` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `attack_report` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=70 ;
 
 
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `date` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;
 
 
CREATE TABLE IF NOT EXISTS `block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_adress` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `ban_date` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `ban_reason` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `first_conn` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `last_conn` text CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;