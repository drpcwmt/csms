CREATE TABLE `absents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `con_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `justify` int(11) NOT NULL DEFAULT '0',
  `ill` int(11) NOT NULL DEFAULT '0',
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `absents_bylesson` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `std_id` int(11) NOT NULL,
  `rec_id` int(11) NOT NULL,
  `lesson_no` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;CREATE TABLE `behavior` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `std_id` int(11) NOT NULL,
  `lesson_no` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `pattern` text CHARACTER SET utf8 COLLATE utf8_bin,
  `sanction` text CHARACTER SET utf8 COLLATE utf8_bin,
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin,
  `user_id` int(11) NOT NULL,
  `msg` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;CREATE TABLE `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_ltr` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name_rtl` varchar(64) DEFAULT NULL,
  `level_id` int(11) NOT NULL,
  `resp` int(11) DEFAULT NULL,
  `room_no` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=184 DEFAULT CHARSET=latin1;CREATE TABLE `classes_std` (
  `class_id` int(11) NOT NULL,
  `std_id` int(11) NOT NULL,
  `new_stat` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `begin_date` int(11) NOT NULL,
  `end_date` int(11) DEFAULT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin,
  `begin_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `private` int(11) NOT NULL DEFAULT '0',
  `lesson_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;CREATE TABLE `events_con` (
  `event_id` int(11) NOT NULL,
  `con` varchar(16) NOT NULL,
  `con_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `exams` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) DEFAULT NULL,
  `service` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `exam_no` int(11) NOT NULL,
  `min` int(11) NOT NULL DEFAULT '0',
  `max` int(11) NOT NULL DEFAULT '0',
  `title` varchar(65) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `coef` decimal(11,1) NOT NULL DEFAULT '1.0',
  `con` varchar(6) NOT NULL,
  `con_id` int(11) DEFAULT NULL,
  `value` decimal(11,1) DEFAULT NULL,
  `approved` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38605 DEFAULT CHARSET=latin1;CREATE TABLE `exams_makeup` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `exam_date` int(11) NOT NULL,
  `min` int(11) NOT NULL,
  `max` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;CREATE TABLE `exams_results` (
  `exam_id` int(11) NOT NULL,
  `std_id` int(11) NOT NULL,
  `results` decimal(11,1) NOT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `final_result` (
  `std_id` int(11) NOT NULL,
  `result` int(11) DEFAULT NULL,
  `services` text CHARACTER SET utf8 COLLATE utf8_bin,
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin,
  `class_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `level_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  UNIQUE KEY `std_id` (`std_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `parent` varchar(12) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `resp` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;CREATE TABLE `groups_std` (
  `group_id` int(11) NOT NULL,
  `std_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dates` varchar(64) NOT NULL,
  `con` varchar(12) DEFAULT NULL,
  `con_id` int(11) DEFAULT NULL,
  `comments` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=221 DEFAULT CHARSET=latin1;CREATE TABLE `marks_addon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `max` int(11) NOT NULL,
  `optional` int(11) NOT NULL DEFAULT '0',
  `min` int(11) NOT NULL,
  `bonus` int(11) NOT NULL DEFAULT '0',
  `term_id` int(11) DEFAULT '0',
  `level_id` int(11) DEFAULT NULL,
  `value` decimal(11,1) DEFAULT '0.0',
  `coef` decimal(11,1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;CREATE TABLE `marks_addon_results` (
  `add_id` int(11) NOT NULL,
  `std_id` int(11) NOT NULL,
  `results` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `materials_classes` (
  `class_id` int(11) NOT NULL,
  `services` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `materials_groups` (
  `group_id` int(11) NOT NULL,
  `services` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `materials_profs` (
  `prof_id` int(11) NOT NULL,
  `services` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `materials_std` (
  `std_id` int(11) NOT NULL,
  `services` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `out_permis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `std_id` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `hour` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `out_by` varchar(6) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `pers` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `till` int(11) DEFAULT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `schedules_date` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `con` varchar(12) NOT NULL,
  `con_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10019 DEFAULT CHARSET=latin1;CREATE TABLE `schedules_homework` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `exercise_id` int(11) DEFAULT NULL,
  `answer_date` int(11) DEFAULT NULL,
  `online` int(11) NOT NULL DEFAULT '1',
  `mark` int(11) DEFAULT NULL,
  `attaches` text CHARACTER SET latin1 COLLATE latin1_danish_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8597 DEFAULT CHARSET=latin1;CREATE TABLE `schedules_lessons` (
  `rec_id` int(11) NOT NULL,
  `lesson_no` int(11) NOT NULL,
  `services` int(11) NOT NULL,
  `prof` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `hall` varchar(16) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `tools` text,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exam` int(11) NOT NULL,
  `rule` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18612 DEFAULT CHARSET=latin1;CREATE TABLE `schedules_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lesson_id` int(11) NOT NULL,
  `content` text COLLATE utf8_bin NOT NULL,
  `owner_id` int(11) NOT NULL,
  `shared` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=279 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `schedules_static_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `con` varchar(16) NOT NULL,
  `con_id` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `begin` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `schedules_times` (
  `begin` int(11) NOT NULL,
  `end` int(11) NOT NULL,
  `rec_id` int(11) NOT NULL,
  `lesson_no` int(11) NOT NULL,
  `type` varchar(1) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `level_id` int(11) NOT NULL,
  `mat_id` int(11) NOT NULL,
  `target` int(11) DEFAULT NULL,
  `target_unit` varchar(12) COLLATE utf8_bin NOT NULL,
  `optional` int(11) NOT NULL DEFAULT '0',
  `bonus` int(11) NOT NULL DEFAULT '0',
  `exam_no` int(11) NOT NULL DEFAULT '0',
  `mark` int(11) NOT NULL DEFAULT '1',
  `coef` decimal(1,0) NOT NULL DEFAULT '1',
  `schedule` int(1) NOT NULL,
  `fail` varchar(12) COLLATE utf8_bin DEFAULT NULL,
  `min` varchar(12) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=593 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;CREATE TABLE `services_subs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `name_ltr` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name_rtl` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;CREATE TABLE `terms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_no` int(11) NOT NULL,
  `title` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `begin_date` int(11) DEFAULT NULL,
  `end_date` int(11) DEFAULT NULL,
  `marks` int(11) NOT NULL,
  `level_id` int(11) DEFAULT NULL,
  `exam_no` int(11) DEFAULT '0',
  `approved` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=436 DEFAULT CHARSET=latin1;CREATE TABLE `terms_apprc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `std_id` int(11) NOT NULL,
  `term_id` int(11) NOT NULL,
  `service` int(11) NOT NULL,
  `comments` text CHARACTER SET utf8 COLLATE utf8_bin,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1