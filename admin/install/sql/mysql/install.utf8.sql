-- -------------------------------------------------------------------- --
-- Phoca Panorama manual installation                                   --
-- -------------------------------------------------------------------- --
-- See documentation on https://www.phoca.cz/                            --
--                                                                      --
-- Change all prefixes #__ to prefix which is set in your Joomla! site  --
-- (e.g. from #__phocapanorama to jos_phocapanorama)                    --
-- Run this SQL queries in your database tool, e.g. in phpMyAdmin       --
-- If you have questions, just ask in Phoca Forum                       --
-- https://www.phoca.cz/forum/                                           --
-- -------------------------------------------------------------------- --

CREATE TABLE IF NOT EXISTS `#__phocapanorama_categories` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `description` text,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` int(11) unsigned NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `count` int(11) NOT NULL default '0',
  `hits` int(11) NOT NULL default '0',
  `params` text,
  `metakey` text,
  `metadesc` text,
  `language` char(7) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `idx_checkout` (`checked_out`)
) default CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__phocapanorama_items` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `title` varchar(250) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `folder` varchar(250) NOT NULL default '',
  `iframe_link` text,
  `image` varchar(250) NOT NULL default '',
  `description` text,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `hits` int(11) NOT NULL default '0',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `access` int(11) unsigned NOT NULL default '0',
  `params` text,
  `metakey` text,
  `metadesc` text,
  `language` char(7) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `catid` (`catid`,`published`)
) default CHARSET=utf8;

-- upgrade 3.0.0 beta to 3.0.0 stable
-- ALTER TABLE `#__phocapanorama_items` ADD COLUMN `iframe_link` text;
