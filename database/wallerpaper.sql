CREATE TABLE `wallpaper` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(32) NOT NULL DEFAULT '' COMMENT '文件名称',
  `type` tinyint(4) NOT NULL,
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `impression` int(11) NOT NULL DEFAULT '0' COMMENT '曝光次数',
  `hash_code` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;