-- Create the get_variant_multiplier function on the server
-- This function is required by the trigger: recalculate_variant_prices_on_update

DELIMITER $$

DROP FUNCTION IF EXISTS `get_variant_multiplier`$$

CREATE FUNCTION `get_variant_multiplier`(attr_value VARCHAR(255)) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
    DECLARE quantity DECIMAL(10,2);
    DECLARE unit VARCHAR(10);
    DECLARE multiplier DECIMAL(10,2);

    SET attr_value = LOWER(TRIM(attr_value));

    IF attr_value REGEXP '^([0-9.]+)\\s*(gm|kg|ml|ltr|l|pc|pcs|dozen|dz)?' THEN
        SET quantity = CAST(REGEXP_SUBSTR(attr_value, '^([0-9.]+)') AS DECIMAL(10,2));
        SET unit = REGEXP_SUBSTR(attr_value, '(gm|kg|ml|ltr|l|pc|pcs|dozen|dz)');

        CASE unit
            WHEN 'gm' THEN SET multiplier = quantity / 1000;
            WHEN 'kg' THEN SET multiplier = quantity;
            WHEN 'ml' THEN SET multiplier = quantity / 1000;
            WHEN 'ltr' THEN SET multiplier = quantity;
            WHEN 'l' THEN SET multiplier = quantity;
            WHEN 'pc' THEN SET multiplier = quantity;
            WHEN 'pcs' THEN SET multiplier = quantity;
            WHEN 'dozen' THEN SET multiplier = quantity * 12;
            WHEN 'dz' THEN SET multiplier = quantity * 12;
            ELSE SET multiplier = quantity;
        END CASE;
    ELSE
        SET multiplier = 1;
    END IF;

    RETURN multiplier;
END$$

DELIMITER ;
