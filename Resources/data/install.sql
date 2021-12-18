CREATE TABLE IF NOT EXISTS PREFIX_additional_image (
    `id_additional_image` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_product` INT(11) UNSIGNED NOT NULL,
    `extension` VARCHAR(6) NOT NULL,
    PRIMARY KEY(`id_additional_image`)
) ENGINE = DB_ENGINE DEFAULT CHARSET = utf8;