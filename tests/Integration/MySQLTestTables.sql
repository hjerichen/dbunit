DROP TABLE IF EXISTS `product`;
CREATE TABLE `product`
(
    `id`    int(10) unsigned NOT NULL AUTO_INCREMENT,
    `ean`   varchar(255) DEFAULT NULL,
    `stock` int(11)      DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;