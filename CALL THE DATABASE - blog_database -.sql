-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2021 at 06:43 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blog_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `article-comments`
--

CREATE TABLE `article-comments` (
  `articleId` int(11) NOT NULL,
  `commentId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `article-comments`
--

INSERT INTO `article-comments` (`articleId`, `commentId`) VALUES
(21, 64),
(21, 66),
(29, 68),
(29, 69),
(29, 70),
(29, 71),
(29, 72),
(28, 73),
(28, 74),
(28, 75),
(32, 76),
(32, 77);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `articleId` int(11) NOT NULL,
  `category` int(11) NOT NULL,
  `title` varchar(55) NOT NULL,
  `publisher` int(11) DEFAULT NULL,
  `dateOfPublish` timestamp NOT NULL DEFAULT current_timestamp(),
  `publishedText` text NOT NULL,
  `image` int(11) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`articleId`, `category`, `title`, `publisher`, `dateOfPublish`, `publishedText`, `image`, `visible`) VALUES
(1, 1, 'test with line break', 5, '2021-05-20 21:45:45', '<p>hello</p>\r\n<p>this</p>\r\n<p>is</p>\r\n<p>a</p>\r\n<p>test</p>\r\n<p>to</p>\r\n<p>see</p>\r\n<p>how</p>\r\n<p>my</p>\r\n<p>formatting</p>\r\n<p>works</p>', NULL, 0),
(2, 1, 'fgdfg', 5, '2021-05-20 21:48:07', '<p>why tas</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>teh does it do this</p>', NULL, 0),
(3, 1, 'dfgdfg', 5, '2021-05-20 21:50:25', '<h1>asdasd</h1>\r\n<p>asd</p>\r\n<p>asd</p>\r\n<p>asd</p>\r\n<p>asd</p>\r\n<p>asda</p>\r\n<p>sd</p>', NULL, 0),
(4, 1, 'df', 5, '2021-05-20 21:51:40', 'asdasd<br /><br /><br /><br /><br /><br /><br />asd<br /><br /><br /><br />asdasd', NULL, 0),
(5, 1, 'www', 5, '2021-05-20 21:56:44', 'test<br />wtih <br />the line<br />break again<br />teehee<br />asd', NULL, 0),
(6, 1, 'asd', 5, '2021-05-20 22:04:46', 'hey<br />this<br />is<br />my<br />first<br />time<br />posting<br />here<br />whats<br />going<br />on<br />bros', NULL, 0),
(7, 1, 'asd', -1, '2021-05-21 17:48:22', 'hey &lt;3 &gt;', NULL, 1),
(8, 1, 'trest', -1, '2021-05-21 17:50:53', 'nyah how are you <br /><br />sup<br /><br />bro<br /><br />this<br /><br /><br />is<br /><br /><br />really<br /><br /><br />cool', NULL, 0),
(9, 1, 'ss', -1, '2021-05-21 17:52:35', 'hey<br />this<br />is<br />a<br />new<br />line<br />test', NULL, 0),
(10, 1, 'asd', -1, '2021-05-21 17:53:46', 'hey<br />this<br />is<br />a<br />new<br />post<br />to<br />test<br />my<br />new<br />line<br />feature', NULL, 0),
(11, 1, 'sssss', -1, '2021-05-21 17:56:58', 'hey<br />what\'s going on<br />my friend<br />this is another<br />post to see<br />how my remove breaks thing is working<br />lets see if it works', NULL, 0),
(12, 1, '123', -1, '2021-05-21 17:59:58', 'once again<br />i am here<br />testing<br />my new line<br />removing<br />feature to see <br />if it removes text<br />that its not supposed to<br />asd', NULL, 0),
(13, 1, 'asd', -1, '2021-05-21 18:00:49', 'hey<br />what\'s going on<br />my friend<br />this is another<br />post to see<br />how my<br />remove<br />breaks thing is<br />workin <br />lets see if it works', NULL, 0),
(14, 1, 'asd', -1, '2021-05-21 18:08:56', '<p>hey<br />this<br />is<br />another<br />test<br />now<br />to<br />see<br />if<br />my<br />lexer<br />works</p>', NULL, 1),
(15, 1, 'asd', -1, '2021-05-21 18:15:10', 'hey<br />this<br />is<br />another<br />test<br />now<br />to<br />see<br />if <br />hey<br />this<br />is<br />another<br />test<br />now<br />to<br />see<br />if hey<br />this<br />is<br />another<br />test<br />now<br />to<br />see<br />if hey<br />this<br />is<br />another<br />test<br />now<br />to<br />see<br />if hey<br />this<br />is<br />another<br />test<br />now<br />to<br />see<br />if', NULL, 0),
(16, 1, 'asd', -1, '2021-05-21 18:20:30', 'alsdjaklsdjalsdalsdjaklsdjalsd<br />alsdjaklsdjalsd<br />alsdjaklsdjalsd<br />alsdjaklsdjalsdalsdjaklsdjalsdalsdjaklsdjalsd<br /><br />alsdjaklsdjalsd<br />alsdjaklsdjalsdalsdjaklsdjalsd<br />alsdjaklsdjalsdalsdjaklsdjalsdalsdjaklsdjalsd<br /><br /><br />alsdjaklsdjalsd<br />alsdjaklsdjalsd<br /><br />alsdjaklsdjalsd<br />alsdjaklsdjalsd<br />alsdjaklsdjalsdalsdjaklsdjalsdalsdjaklsdjalsdalsdjaklsdjalsd<br /><br />alsdjaklsdjalsd<br /><br />alsdjaklsdjalsdalsdjaklsdjalsd<br />alsdjaklsdjalsd<br />alsdjaklsdjalsd<br /><br />alsdjaklsdjalsdand stuff in between[12:01 PM] Queen Isabelle: But where do we go from there?<br />[12:02 PM] Untitled.png: sub x = 1 and y = 3<br />[12:03 PM] Queen Isabelle: It&rsquo;s 2, and then would it still be equal to dy/dx?<br />[12:03 PM] Untitled.png: yeah<br />[12:03 PM] Queen Isabelle: Alright<br />[12:04 PM] Queen Isabelle: It just seems weird to sub in the y<br />[12:08 PM] Untitled.png: what did you get for your answer<br />[12:08 PM] Untitled.png: i got -2/9<br />alsdjaklsdjalsdalsdjaklsdjalsd', 20, 0),
(17, 1, 'asd', -1, '2021-05-21 18:21:03', '<p>alsdjaklsdjalsdalsdjaklsdjalsd<br />alsdjaklsdjalsd<br />alsdjaklsdjalsd<br />alsdjaklsdjalsdalsdjaklsdjalsdalsdjaklsdjalsd<br /><br />alsdjaklsdjalsd<br />alsdjaklsdjalsdalsdjaklsdjalsd<br />alsdjaklsdjalsdalsdjaklsdjalsdalsdjaklsdjalsd<br /><br /><br />alsdjaklsdjalsd<br />alsdjaklsdjalsd<br /><br />alsdjaklsdjalsd<br />alsdjaklsdjalsd<br />alsdjaklsdjalsdalsdjaklsdjalsdalsdjaklsdjalsdalsdjaklsdjalsd<br /><br />alsdjaklsdjalsd<br /><br />alsdjaklsdjalsdalsdjaklsdjalsd<br />alsdjaklsdjalsd<br />alsdjaklsdjalsd<br /><br />alsdjaklsdjalsdand stuff in between[12:01 PM] Queen Isabelle: But where do we go from there?<br />[12:02 PM] Untitled.png: sub x = 1 and y = 3<br />[12:03 PM] Queen Isabelle: It&rsquo;s 2, and then would it still be equal to dy/dx?<br />[12:03 PM] Untitled.png: yeah<br />[12:03 PM] Queen Isabelle: Alright<br />[12:04 PM] Queen Isabelle: It just seems weird to sub in the y<br />[12:08 PM] Untitled.png: what did you get for your answer<br />[12:08 PM] Untitled.png: i got -2/9<br />alsdjaklsdjalsdalsdjaklsdjalsd</p><br><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', NULL, 1),
(19, 1, 'sup guys', -1, '2021-05-22 21:32:32', 'alskdjalskdjalksjdalksjdlalksdj', NULL, 0),
(20, 11, 'salkdjaslkdj', 5, '2021-05-24 21:53:37', 'laksjdlaksjdlaksjdlaksjdalksjdlalskdasdasdasdasdasdasd', 21, 1),
(21, 1, 'lak', 5, '2021-05-24 21:54:09', '<p>laky laky</p>\r\n<p>asdasdasdasdasdasdasd</p>\r\n<p>nyah this is another edit</p>', 22, 1),
(22, 1, 'pp', 5, '2021-05-24 21:57:14', '<p>dfddd</p>\r\n<p>hahaha i\'m an admin you cannot stop me!</p><br><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', 23, 1),
(24, 1, 'new thread', -1, '2021-05-25 02:20:55', '<p>howdy guys this is my first post here</p>\r\n<p>this is my first edit on this platform</p>\r\n<p>asdasdasdasdasdasdasdasd<br /><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p><br><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', NULL, 1),
(25, 1, 'new post', -1, '2021-05-25 02:32:42', '<p>hello guys this is an edit nyah</p>\r\n<p>nyahnyagh12asdasdasdasd<br /><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p>\r\n<p><br /><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p>\r\n<p><br /><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p><br><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', NULL, 1),
(26, 1, 'dfgdfg', 5, '2021-05-25 03:11:49', 'fgdfg', NULL, 1),
(27, 1, 'nyah', 5, '2021-05-25 03:12:47', 'nyah', NULL, 1),
(28, 1, 'asdasd', 5, '2021-05-25 03:15:12', '<p>asdasd</p><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', 31, 1),
(29, 1, 'asdasd', 5, '2021-05-25 03:15:23', '<p>asdasd</p>\r\n<p><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p>\r\n<p><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p>\r\n<p><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p>\r\n<p><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p>\r\n<p><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p>\r\n<p><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p>\r\n<p><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', 25, 1),
(30, 2, 'test', -1, '2021-05-25 16:14:12', 'asdasdk', NULL, 1),
(31, 2, 'this should become anon after user delte', -1, '2021-05-25 16:24:19', 'asdasdasdasd', NULL, 1),
(32, 1, 'test', 5, '2021-05-25 16:38:32', 'test', 4, 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `categoryId` int(11) NOT NULL,
  `categoryName` varchar(55) NOT NULL,
  `categoryDescription` varchar(140) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`categoryId`, `categoryName`, `categoryDescription`, `visible`) VALUES
(1, 'random', '<p>The stories and information posted here are artistic</p>', 1),
(2, 'anime & manga', '<p>Omae wa mou shindeiru. NANI!?!?!??!</p>', 1),
(3, 'cute', 'OwO?!, what\'s this??! Cute things and the like!?!?', 1),
(4, 'wallpapers', 'Mystic and memorable papers of the walls. Such beauty should be shared with the world.', 1),
(11, 'oekaki', 'Doodles, Drawings, and ****. \"To Draw A Picture\"', 1),
(12, 'digits', 'For digit enthusiasts and people of culture.', 1),
(14, 'asdasd', 'asdasd', 0),
(16, 'People', '<p>all about famous people</p>', 1);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `commentId` int(11) NOT NULL,
  `publisher` int(11) NOT NULL,
  `comment` text NOT NULL,
  `publishDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` int(11) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`commentId`, `publisher`, `comment`, `publishDate`, `image`, `visible`) VALUES
(64, -1, '<p>nyah fuck mehjghjghj</p>\r\n<p><br /><br /><span style=\"color: red;\">This Post Has Been Edited By An Administrator</span></p><br><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', '2021-05-24 22:01:29', NULL, 0),
(66, -1, '<p><label class=\"buttonLink\" style=\"letter-spacing: 0;\">&gt;&gt;T21 </label>&nbsp;<br />sup</p>\r\n<p>this is an edit</p>\r\n<p>this is another edit</p>', '2021-05-25 02:47:24', NULL, 1),
(67, 9, 'asd', '2021-05-25 03:19:03', 16, 1),
(68, -1, '<p><label class=\"buttonLink\" style=\"letter-spacing: 0;\">&gt;&gt;T29 </label>&nbsp;<br />test posting image</p><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', '2021-05-25 03:23:02', 32, 1),
(69, -1, '&lt;@T29&gt;<br />another image test', '2021-05-25 03:24:21', NULL, 1),
(70, -1, '&lt;@T29&gt;<br />another image test', '2021-05-25 03:25:56', NULL, 1),
(71, -1, '&lt;@T29&gt;<br />another test', '2021-05-25 03:26:52', NULL, 1),
(72, -1, '&lt;@T29&gt;asdasdasd', '2021-05-25 03:27:56', 26, 1),
(73, -1, '<p><label class=\"buttonLink\" style=\"letter-spacing: 0;\">&gt;&gt;T28 </label>&nbsp;<br />hey</p><br><span style=\'color:red;\'>This Post Has Been Edited By An Administrator</span>', '2021-05-25 03:42:12', 30, 1),
(74, -1, '&lt;@T28&gt;', '2021-05-25 03:55:02', 28, 1),
(75, -1, '&lt;@C73&gt;', '2021-05-25 03:55:28', 29, 1),
(76, 5, '&lt;@T32&gt;<br />test', '2021-05-25 16:38:43', NULL, 1),
(77, 5, '&lt;@C76&gt;<br />test', '2021-05-25 16:38:55', 33, 1);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `imageId` int(11) NOT NULL,
  `imagePath` varchar(500) NOT NULL,
  `imageHash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`imageId`, `imagePath`, `imageHash`) VALUES
(4, 'imgs\\upload\\35e98272244ebf715fc9884f95fa4424a15a074eeaee918204adaa0242bf82be.png', '35e98272244ebf715fc9884f95fa4424a15a074eeaee918204adaa0242bf82be'),
(9, 'imgs\\upload\\f28287bbf4b6f8bbf3c437ee11d7f81375e1bedcafed7c66a843e91a7296112c.jpg', 'f28287bbf4b6f8bbf3c437ee11d7f81375e1bedcafed7c66a843e91a7296112c'),
(12, 'imgs\\upload\\2a485e6fc82f0d641622db79127045d0b4c31a00ced340c0c8ddf922d4cc0fa2.jpg', '2a485e6fc82f0d641622db79127045d0b4c31a00ced340c0c8ddf922d4cc0fa2'),
(13, 'imgs\\upload\\76b8a7144f8a5a739dd09e7e9ee46ce6bfdb93cb968fc10a0192a119ca040c11.png', '76b8a7144f8a5a739dd09e7e9ee46ce6bfdb93cb968fc10a0192a119ca040c11'),
(14, 'imgs\\upload\\084e16a922113d34ed68984b8563a1020fab3441fc81e7a8d215d1162f8bc28d.png', '084e16a922113d34ed68984b8563a1020fab3441fc81e7a8d215d1162f8bc28d'),
(15, 'imgs\\upload\\3be1552fdd6437ee0104ca34ad97431efb526f5f95b81392d3193dcea16d5bee.png', '3be1552fdd6437ee0104ca34ad97431efb526f5f95b81392d3193dcea16d5bee'),
(16, 'imgs\\upload\\a6c3bc94cca0adefca311abf9b0870278eff4d3aee91370d401d3f47364f03e5.jpg', 'a6c3bc94cca0adefca311abf9b0870278eff4d3aee91370d401d3f47364f03e5'),
(17, 'imgs\\upload\\b2ee5009a5cb44cc88d25ed0d6bfa2fa81ed5d9757eeb486d379483ab1f30ac9.png', 'b2ee5009a5cb44cc88d25ed0d6bfa2fa81ed5d9757eeb486d379483ab1f30ac9'),
(18, 'imgs\\upload\\757e3b2d00c4cabe200169524b25ccba57a1320c11830bf6c51cd5f97a19c9cf.png', '757e3b2d00c4cabe200169524b25ccba57a1320c11830bf6c51cd5f97a19c9cf'),
(19, 'imgs\\upload\\865bcdc070ddc59fbfdc65a6bb5b0b45f3a64dcce4889fcbd931ab9eb740fdd3.png', '865bcdc070ddc59fbfdc65a6bb5b0b45f3a64dcce4889fcbd931ab9eb740fdd3'),
(20, 'imgs\\upload\\26966a15c853ee0c5f1b6dd0b0471e6654cd645f2f6565f422e8ecaed3b87e1a.png', '26966a15c853ee0c5f1b6dd0b0471e6654cd645f2f6565f422e8ecaed3b87e1a'),
(21, 'imgs\\upload\\9860d5eb1384c3df94acf7afb9090fab104cc7490d065e4d6b6fe87de5276be6.png', '9860d5eb1384c3df94acf7afb9090fab104cc7490d065e4d6b6fe87de5276be6'),
(22, 'imgs\\upload\\a05b09c44b949e83ce564bb3aa840b76d85b97efc320ab279f72247d97c1a700.png', 'a05b09c44b949e83ce564bb3aa840b76d85b97efc320ab279f72247d97c1a700'),
(23, 'imgs\\upload\\230e030e6e47dae0316088d44d10f4e0acedc260d68a1751c79c643e400b53f2.jpg', '230e030e6e47dae0316088d44d10f4e0acedc260d68a1751c79c643e400b53f2'),
(24, 'imgs\\upload\\259ffb2b3d1c0e26bacdf7c2855aec03792a7fbe90d0f686bc63d9517a2ea612.png', '259ffb2b3d1c0e26bacdf7c2855aec03792a7fbe90d0f686bc63d9517a2ea612'),
(25, 'imgs\\upload\\544132f152cf8ca839185e580fd0738103b0fff6eb757c6d35823adbeaed3725.jpg', '544132f152cf8ca839185e580fd0738103b0fff6eb757c6d35823adbeaed3725'),
(26, 'imgs\\upload\\10436215a059d7723c0eb4cffdc8540e24b6db1b7e7b9e089d85df0e364cbefc.png', '10436215a059d7723c0eb4cffdc8540e24b6db1b7e7b9e089d85df0e364cbefc'),
(27, 'imgs\\upload\\c8beece994eef2f6d4d3040c079658b9e6d1a445b96a2cb383a07b3a0d5a6393.jfif', 'c8beece994eef2f6d4d3040c079658b9e6d1a445b96a2cb383a07b3a0d5a6393'),
(28, 'imgs\\upload\\629b2cbfe9d32672ead19cb81a9b8ebeb06aab7c0e53c826fa73ca5695d64ffc.jpg', '629b2cbfe9d32672ead19cb81a9b8ebeb06aab7c0e53c826fa73ca5695d64ffc'),
(29, 'imgs\\upload\\c14cba194e42b0053a2e412f4d90a4bc974e1b7ffa9cfd4c4513a7d57fc3c172.jpg', 'c14cba194e42b0053a2e412f4d90a4bc974e1b7ffa9cfd4c4513a7d57fc3c172'),
(30, 'imgs\\upload\\26de5e3b20703b32a4399f43f30fdb146e066620f1026f5b4279dc794a0c4dbe.png', '26de5e3b20703b32a4399f43f30fdb146e066620f1026f5b4279dc794a0c4dbe'),
(31, 'imgs\\upload\\d0f85ffaa6da8dca3423c41c00baa36888f29bf8e09bee2c0dc3e9e375d112b1.png', 'd0f85ffaa6da8dca3423c41c00baa36888f29bf8e09bee2c0dc3e9e375d112b1'),
(32, 'imgs\\upload\\a151ae222e0de3cecde813d44fa6b44e6d4724bca39ac4052bc727c3ad53dd5f.png', 'a151ae222e0de3cecde813d44fa6b44e6d4724bca39ac4052bc727c3ad53dd5f'),
(33, 'imgs//upload//ab1b9d5eb96daa587c4e658c80ebb40f893b24597d4ac8101286fc069387006b.png', 'ab1b9d5eb96daa587c4e658c80ebb40f893b24597d4ac8101286fc069387006b');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `permissionId` int(11) NOT NULL,
  `permissionName` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`permissionId`, `permissionName`) VALUES
(0, 'suspended'),
(1, 'basic user'),
(2, 'admin'),
(3, 'moderator');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int(11) NOT NULL,
  `username` varchar(55) NOT NULL,
  `email` varchar(55) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permissions` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `username`, `email`, `password`, `permissions`) VALUES
(-1, 'Anonymous', '', '', 1),
(2, 'nyayners', 'nyah@gmail.com', '123', 1),
(4, 'mr tom ;3', 't@gmails.com', '$2y$10$KQr07xSowdni4oXkmFL1rOC1wsYmSZAQNfNp/rkD1A88xbvHeDVZ6', 0),
(5, 'Nyah', 'a@a.ca', '$2y$10$qGeqgKB97bwYHDGNkjXNou7n6uMZf7qMlN9B9XjXOkd039IwokOu6', 2),
(6, 'Kowandiki', 'kowandiki@gmail.com', '$2y$10$nEALhW/El0hyyqDyCP7vLOhhP24.HvLabOyiAjP1bEOBuhw9Z1RIa', 1),
(9, 'and', 'a@aa.ca', '$2y$10$xgaQ6tuktAmHY.Uq7NqS6e2K9UA3iHgYFumxAu5tfDaZYqSscPtPS', 2),
(10, 'nyasdasdasd', 'tt@tt.ca', '$2y$10$0bwS0vmjjusDZpBv8T2SdO3XacQZ0dKSrMJmvy/E3UUKQiIbR0/k.', 0),
(11, 'teehee', '1@1.1', '$2y$10$HLSmNicvrZSjDzUawKOi4u8pgVBNNVxoHuApCtQ1jj3cxzfeoYCJy', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `article-comments`
--
ALTER TABLE `article-comments`
  ADD KEY `articleId` (`articleId`),
  ADD KEY `commentId` (`commentId`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`articleId`),
  ADD KEY `publisher` (`publisher`),
  ADD KEY `category` (`category`),
  ADD KEY `image` (`image`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categoryId`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`commentId`),
  ADD KEY `publisher` (`publisher`),
  ADD KEY `image` (`image`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`imageId`),
  ADD UNIQUE KEY `imageHash` (`imageHash`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`permissionId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD KEY `permissions` (`permissions`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `articleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `categoryId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `commentId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `imageId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `permissionId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `article-comments`
--
ALTER TABLE `article-comments`
  ADD CONSTRAINT `article-comments_ibfk_2` FOREIGN KEY (`commentId`) REFERENCES `comments` (`commentId`) ON DELETE CASCADE,
  ADD CONSTRAINT `article-comments_ibfk_3` FOREIGN KEY (`articleId`) REFERENCES `articles` (`articleId`) ON DELETE CASCADE;

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`category`) REFERENCES `categories` (`categoryId`) ON DELETE CASCADE,
  ADD CONSTRAINT `articles_ibfk_4` FOREIGN KEY (`image`) REFERENCES `images` (`imageId`) ON DELETE SET NULL,
  ADD CONSTRAINT `articles_ibfk_5` FOREIGN KEY (`publisher`) REFERENCES `users` (`userId`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`image`) REFERENCES `images` (`imageId`) ON DELETE SET NULL,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`publisher`) REFERENCES `users` (`userId`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`permissions`) REFERENCES `permissions` (`permissionId`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
