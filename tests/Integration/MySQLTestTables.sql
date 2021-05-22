DROP TABLE IF EXISTS `productExtension`;
DROP TABLE IF EXISTS `product`;

CREATE TABLE `product`
(
    `id`    int(10) unsigned NOT NULL AUTO_INCREMENT,
    `ean`   varchar(255) DEFAULT NULL,
    `stock` int(11)      DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

CREATE TABLE `productExtension`
(
    `id`        int(10) unsigned NOT NULL AUTO_INCREMENT,
    `productId` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `IDX_DFC693F764B64DCC` (`productId`),
    CONSTRAINT `FK_DFC693F75AF690F3` FOREIGN KEY (`productId`) REFERENCES `product` (`id`)
) ENGINE = InnoDB;