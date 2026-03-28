CREATE TABLE IF NOT EXISTS `mc_plug_advmulti` (
    `id_advmulti` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `icon_advmulti` varchar(80) NOT NULL,
    `module_advmulti` varchar(50) NOT NULL DEFAULT 'home',
    `id_module` int(11) unsigned NOT NULL DEFAULT 0,
    `order_advmulti` smallint(5) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_advmulti`),
    KEY `idx_module` (`module_advmulti`, `id_module`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `mc_plug_advmulti_content` (
    `id_advmulti_content` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `id_advmulti` int(11) unsigned NOT NULL,
    `id_lang` smallint(3) unsigned NOT NULL,
    `url_advmulti` varchar(255) DEFAULT NULL,
    `blank_advmulti` tinyint(1) unsigned NOT NULL DEFAULT 0,
    `title_advmulti` varchar(150) NOT NULL,
    `desc_advmulti` text,
    `published_advmulti` tinyint(1) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id_advmulti_content`),
    KEY `id_lang` (`id_lang`),
    KEY `id_advmulti` (`id_advmulti`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;

ALTER TABLE `mc_plug_advmulti_content`
    ADD CONSTRAINT `mc_plug_advmulti_content_ibfk_1` FOREIGN KEY (`id_advmulti`) REFERENCES `mc_plug_advmulti` (`id_advmulti`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mc_plug_advmulti_content_ibfk_2` FOREIGN KEY (`id_lang`) REFERENCES `mc_lang` (`id_lang`) ON DELETE CASCADE ON UPDATE CASCADE;