-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 13, 2011 at 07:56 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `aud2`
--

USE aud2;

-- --------------------------------------------------------

--
-- Table structure for table `playlists`
--

CREATE TABLE `playlists` (
  `playlistid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`playlistid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `playlistsongs`
--

CREATE TABLE `playlistsongs` (
  `psid` int(11) NOT NULL AUTO_INCREMENT,
  `playlistid` int(11) NOT NULL,
  `songid` int(11) NOT NULL,
  PRIMARY KEY (`psid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `songs`
--

CREATE TABLE `songs` (
  `songid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `songpath` varchar(255) NOT NULL,
  `artist` varchar(50) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `album` varchar(50) DEFAULT NULL,
  `track` varchar(50) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `year` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`songid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
