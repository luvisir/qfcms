-- phpMyAdmin SQL Dump
-- version 3.4.10.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2015 年 10 月 16 日 12:20
-- 服务器版本: 5.5.20
-- PHP 版本: 5.3.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `cms`
--

-- --------------------------------------------------------

--
-- 表的结构 `cms_album`
--

CREATE TABLE IF NOT EXISTS `cms_album` (
  `catID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catPath` varchar(100) NOT NULL DEFAULT '',
  `catTitle` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `catImage` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`catID`),
  KEY `catPath` (`catPath`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `cms_album`
--

INSERT INTO `cms_album` (`catID`, `catPath`, `catTitle`, `description`, `catImage`) VALUES
(1, '0', '????', '???????¼', '');

-- --------------------------------------------------------

--
-- 表的结构 `cms_article`
--

CREATE TABLE IF NOT EXISTS `cms_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `summary` varchar(200) NOT NULL DEFAULT '',
  `postTime` date NOT NULL DEFAULT '0000-00-00',
  `author` varchar(30) NOT NULL DEFAULT '',
  `comeFrom` varchar(50) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `keyword` varchar(20) NOT NULL DEFAULT '',
  `catPath` varchar(200) NOT NULL DEFAULT '',
  `isImg` int(1) unsigned NOT NULL DEFAULT '0',
  `imgName` varchar(50) NOT NULL DEFAULT '',
  `linkPath` varchar(100) NOT NULL DEFAULT '',
  `audit` int(1) unsigned NOT NULL DEFAULT '0',
  `recommend` mediumint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `catPath` (`catPath`),
  KEY `keyword` (`keyword`),
  KEY `isImg` (`isImg`),
  KEY `recommend` (`recommend`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `cms_article`
--

INSERT INTO `cms_article` (`id`, `title`, `summary`, `postTime`, `author`, `comeFrom`, `content`, `keyword`, `catPath`, `isImg`, `imgName`, `linkPath`, `audit`, `recommend`) VALUES
(1, 'dfgsddgdfgdg', 'sdfsdfsdfsdfsdfsdfsdfsdfsdfsdfsdfsdfsdfgdcx\\jhgv', '2015-10-16', 'fd', '', 'vccccccxxxxxxvvbvxcvxcvxvxcvxcv<div>vccccccxxxxxxvvbvxcvxcvxvxcvxcv</div><div>vccccccxxxxxxvvbvxcvxcvxvxcvxcv</div><div>vccccccxxxxxxvvbvxcvxcvxvxcvxcv</div><div>vccccccxxxxxxvvbvxcvxcvxvxcvxcv</div>', 'sdff', '0,1', 0, '/cms/gallery/no_image.gif', '/cms/article/2015-10/20151016-090035.htm', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `cms_cat`
--

CREATE TABLE IF NOT EXISTS `cms_cat` (
  `catID` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catPath` varchar(100) NOT NULL DEFAULT '',
  `catTitle` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `catImage` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`catID`),
  KEY `catPath` (`catPath`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `cms_cat`
--

INSERT INTO `cms_cat` (`catID`, `catPath`, `catTitle`, `description`, `catImage`) VALUES
(1, '0', '???', '???????¼', '');

-- --------------------------------------------------------

--
-- 表的结构 `cms_picture`
--

CREATE TABLE IF NOT EXISTS `cms_picture` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `picTitle` varchar(30) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `picName` varchar(50) NOT NULL DEFAULT '',
  `catPath` varchar(200) NOT NULL DEFAULT '',
  `hasThumb` int(1) NOT NULL DEFAULT '0',
  `hasMark` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `catPath` (`catPath`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `cms_user`
--

CREATE TABLE IF NOT EXISTS `cms_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `pwd` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `cms_user`
--

INSERT INTO `cms_user` (`id`, `name`, `pwd`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
