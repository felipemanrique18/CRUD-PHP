
-- 2. punto a
INSERT INTO `estudiantes` (`nombre`, `apellido`, `identificacion`, `direccion`, `cuidad`, `telefono`, `eps`, `rh`,`fecha_nacimiento`) VALUES ('Jaime ', 'ordoñez', '12367555', 'cra 23#453', 'neiva', '31543568', 'comfamiliar', 'A+','2005-04-02');
INSERT INTO `estudiantes` (`nombre`, `apellido`, `identificacion`, `direccion`, `cuidad`, `telefono`, `eps`, `rh`,`fecha_nacimiento`) VALUES ('Jose ', 'lopez', '13245444', 'cra 3g#453', 'neiva', '324543455', 'comfamiliar', '0+','2005-03-02');


-- 2. punto b
LOAD DATA LOCAL INFILE 'c:/laragon/www/prueba_tecnica/archivo_plano.txt'
INTO TABLE acudientes_estudiantes
FIELDS TERMINATED BY ',' ENCLOSED BY '"' ESCAPED BY '"'
LINES TERMINATED BY ' '



-- //cargar medicos

INSERT INTO `medicos` (`nombre`, `apellido`) VALUES ('yoiner', 'antonio');
INSERT INTO `medicos` (`nombre`, `apellido`) VALUES ('jorge', 'garcia');

-- //realacionar estudiantes con los medicos

INSERT INTO `medicos_estudiantes` (`estudiantes_id`, `medicos_id`) VALUES ('1', '2');
INSERT INTO `medicos_estudiantes` (`estudiantes_id`, `medicos_id`) VALUES ('2', '1');

-- 2. punto c
-- //medicos estudiantes
SELECT e.nombre,e.apellido,e.identificacion,e.eps,m.nombre AS nombre_medico,m.apellido AS apellico_medico FROM estudiantes AS e LEFT JOIN medicos_estudiantes AS me ON me.estudiantes_id = e.id LEFT JOIN medicos AS m ON m.id = me.medicos_id;



-- 2. punto d
-- //cambiar fecha de los primeros 28 estudiantes
DROP PROCEDURE IF EXISTS cambiofecha;
DELIMITER //
CREATE PROCEDURE cambiofecha()
BEGIN
	DECLARE i BIGINT DEFAULT 1;
	DECLARE fecha VARCHAR(24);
  	WHILE i<29 DO
  		SET fecha=CONCAT('2020-04-',i);
  		UPDATE `estudiantes` SET `fecha_nacimiento`=fecha WHERE  (`id`=i);
		SET i=i+1;
	END WHILE;
END;

CALL cambiofecha();


