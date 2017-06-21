CREATE TABLE `alerts` (
  `key_name` varchar(32) NOT NULL,
  `value` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `etablissement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_ltr` varchar(64) COLLATE utf8_bin NOT NULL,
  `name_rtl` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `events_label` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `gradding` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;CREATE TABLE `gradding_points` (
  `gradding_id` int(11) NOT NULL,
  `title` varchar(12) NOT NULL,
  `min` decimal(11,2) DEFAULT NULL,
  `max` decimal(11,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_bin NOT NULL,
  `docs_size` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `guardians` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resp_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `resp_address` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `resp_city` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `resp_zip` int(5) DEFAULT NULL,
  `resp_country` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `resp_job` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `resp_job_address` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `resp_tel` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `resp_mobil` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `resp_mail` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `resp_lang` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `resp_degree` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `halls` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `room_no` int(11) DEFAULT NULL,
  `max_size` int(11) DEFAULT NULL,
  `transfere` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;CREATE TABLE `items_order` (
  `items` varchar(32) COLLATE utf8_bin NOT NULL,
  `order` text COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `levels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `etab_id` int(11) DEFAULT NULL,
  `name_rtl` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `name_ltr` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `calc` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `tot_calc` text CHARACTER SET utf8 COLLATE utf8_bin,
  `gradding` int(11) DEFAULT NULL,
  `cert_templ` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;CREATE TABLE `list_procudures` (
  `name` varchar(256) COLLATE utf8_bin NOT NULL,
  `sql` text COLLATE utf8_bin NOT NULL,
  `select` text COLLATE utf8_bin NOT NULL,
  `order` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `extras` text COLLATE utf8_bin,
  `grouped` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_ltr` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name_rtl` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `color` varchar(8) DEFAULT NULL,
  `schedule` int(11) NOT NULL DEFAULT '1',
  `mark` int(11) NOT NULL DEFAULT '1',
  `optional` int(11) NOT NULL DEFAULT '0',
  `bonus` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;CREATE TABLE `materials_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_rtl` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `name_ltr` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `max` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `parents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `father_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `father_address` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `father_zip` int(5) DEFAULT NULL,
  `father_city` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `father_country` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `father_job` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `father_job_address` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `father_tel` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `father_mobil` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `father_mail` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `father_religion` int(1) DEFAULT NULL,
  `father_lang` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `father_resp` int(11) DEFAULT NULL,
  `father_emp` int(11) NOT NULL DEFAULT '0',
  `mother_name` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `mother_address` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `mother_city` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `mother_zip` int(5) DEFAULT NULL,
  `mother_country` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `mother_job` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `mother_job_address` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `mother_tel` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `mother_mobil` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `mother_mail` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `mother_religion` int(11) DEFAULT NULL,
  `mother_lang` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `mother_resp` int(11) DEFAULT NULL,
  `mother_emp` int(11) NOT NULL DEFAULT '0',
  `father_name_ar` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `father_address_ar` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `father_city_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `father_country_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `mother_name_ar` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `mother_address_ar` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `mother_city_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `mother_country_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1830 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `principals` (
  `id` int(11) NOT NULL,
  `levels` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `privileges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `group` varchar(32) CHARACTER SET latin1 NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `value` int(11) NOT NULL,
  `static` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1734 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `profs` (
  `id` int(11) NOT NULL,
  `max` int(11) NOT NULL,
  `materials` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;CREATE TABLE `profs_materials` (
  `id` int(11) NOT NULL,
  `services` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `settings` (
  `key_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `value` varchar(256) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`key_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `student_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `guardians` text COLLATE utf8_bin,
  `sex` int(1) DEFAULT NULL,
  `religion` int(1) DEFAULT NULL,
  `birth_date` int(11) DEFAULT NULL,
  `tel` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `mail` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `bus_code` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `old_sch` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `old_sch_grade` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `join_date` int(11) DEFAULT NULL,
  `quit_date` int(11) DEFAULT NULL,
  `national_id` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `reg_no` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `locker` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `comment` text COLLATE utf8_bin,
  `status` int(11) NOT NULL DEFAULT '0',
  `lang_1` int(11) DEFAULT NULL,
  `lang_2` int(11) DEFAULT NULL,
  `lang_3` int(11) DEFAULT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL,
  `birth_country` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `birth_city` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `nationality` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `address` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `city` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `country` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `suspension_reason` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `zip` int(5) DEFAULT NULL,
  `name_ar` varchar(256) COLLATE utf8_bin NOT NULL,
  `birth_country_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `birth_city_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `nationality_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `address_ar` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `city_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `country_ar` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `thumb` longblob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1908 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `supervisors` (
  `id` int(11) NOT NULL,
  `services` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `tools` (
  `id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `comments` mediumtext CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `group` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `def_lang` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `css` varchar(64) NOT NULL DEFAULT 'default',
  `last_login` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `waiting_list` (
  `std_id` int(11) NOT NULL,
  `level_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `years` (
  `year` int(11) NOT NULL,
  `user_name` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `begin_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin