-- phpMyAdmin SQL Dump
-- version 2.11.6
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 08 月 25 日 04:41
-- 服务器版本: 5.0.51
-- PHP 版本: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `sina_weibo`
--

-- --------------------------------------------------------

--
-- 表的结构 `user_oauth`
--

CREATE TABLE `user_oauth` (
  `oauth_token` varchar(255) collate utf8_bin NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `oauth_token_secret` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 导出表中的数据 `user_oauth`
--


-- --------------------------------------------------------

--
-- 表的结构 `weibo_wait`
--

CREATE TABLE `weibo_wait` (
  `user_id` int(10) unsigned NOT NULL,
  `time` varchar(12) collate utf8_bin NOT NULL,
  `text` text collate utf8_bin NOT NULL,
  `pic_url` varchar(255) collate utf8_bin NOT NULL,
  `state` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- 导出表中的数据 `weibo_wait`
--

