-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2012 年 02 月 13 日 09:15
-- 服务器版本: 5.1.36
-- PHP 版本: 5.2.10

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `notepad`
--

-- --------------------------------------------------------

--
-- 表的结构 `isay`
--

CREATE TABLE IF NOT EXISTS `isay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text,
  `postime` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `isay`
--

INSERT INTO `isay` (`id`, `content`, `postime`) VALUES
(3, '[o]在线记事本[/o] | [t]开篇[/t]这是一个从云边提取出来的集成UUB编辑器,并依靠SAE强大的支持的在线记事本!及时记录要做的,待做的,小型SOHO的最佳选择哦~\r\n\r\n[img]http://sae.sina.com.cn/static/image/logo.beta.new.png[/img]', '2012-02-13');

-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guest` varchar(40) NOT NULL,
  `message` text NOT NULL,
  `postime` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `message`
--

INSERT INTO `message` (`id`, `guest`, `message`, `postime`) VALUES
(4, '淡淡清香弥漫世界', '速度快,简便轻量的记事本!真是好东西!', '2012-02-13');
