CREATE TABLE IF NOT EXISTS `#__ctransifex_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `lang_name` varchar(255) NOT NULL,
  `completed` int(11) NOT NULL,
  `untranslated_entities` int(11) NOT NULL,
  `translated_entities` int(11) NOT NULL,
  `raw_data` longtext NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ctransifex_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `transifex_slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `alias` varchar(255) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_by_alias` varchar(255) NOT NULL,
  `modified` int(11) NOT NULL,
  `modified_by_alias` varchar(255) NOT NULL,
  `ordering` int(11) NOT NULL,
  `access` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `transifex_config` mediumtext NOT NULL,
  `extension_name` varchar(255) NOT NULL,
  `params` longtext NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ctransifex_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `resource_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ctransifex_zips` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `lang_name` varchar(255) NOT NULL,
  `completed` int(11) NOT NULL,
  `created` date NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
