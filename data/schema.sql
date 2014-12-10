--
-- Table structure for table `pm_conversations`
--

CREATE TABLE IF NOT EXISTS `pm_conversations` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `headline` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_messages`
--

CREATE TABLE IF NOT EXISTS `pm_messages` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `conversation_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` longtext COLLATE utf8_unicode_ci NOT NULL,
  `from_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pm_receivers`
--

CREATE TABLE IF NOT EXISTS `pm_receivers` (
  `id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `conversation_id` varchar(36) COLLATE utf8_unicode_ci DEFAULT NULL,
  `to_user_id` varchar(36) COLLATE utf8_unicode_ci NOT NULL,
  `unread` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Indexes for table `pm_conversations`
--
ALTER TABLE `pm_conversations`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pm_messages`
--
ALTER TABLE `pm_messages`
 ADD PRIMARY KEY (`id`), ADD KEY `IDX_A27723879AC0396` (`conversation_id`);

--
-- Indexes for table `pm_receivers`
--
ALTER TABLE `pm_receivers`
 ADD PRIMARY KEY (`id`), ADD KEY `IDX_689614399AC0396` (`conversation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`user_id`), ADD UNIQUE KEY `UNIQ_1483A5E9F85E0677` (`username`), ADD UNIQUE KEY `UNIQ_1483A5E9E7927C74` (`email`);

--
-- Constraints for table `pm_messages`
--
ALTER TABLE `pm_messages`
ADD CONSTRAINT `FK_A27723879AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `pm_conversations` (`id`);

--
-- Constraints for table `pm_receivers`
--
ALTER TABLE `pm_receivers`
ADD CONSTRAINT `FK_689614399AC0396` FOREIGN KEY (`conversation_id`) REFERENCES `pm_conversations` (`id`);
