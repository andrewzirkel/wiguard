SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `wiguard` DEFAULT CHARACTER SET latin1 ;
USE `wiguard` ;

-- -----------------------------------------------------
-- Table `wiguard`.`DSConfig`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `wiguard`.`DSConfig` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT ,
  `DSServerURL` TEXT NULL DEFAULT NULL ,
  `DSAdminUser` TEXT NULL DEFAULT NULL ,
  `DSAdminPassword` TEXT NULL DEFAULT NULL ,
  `DSIntegrate` TINYINT(1) NULL DEFAULT '0' ,
  PRIMARY KEY (`ID`) )
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;


-- -----------------------------------------------------
-- Table `wiguard`.`auth`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `wiguard`.`auth` (
  `user` VARCHAR(20) NOT NULL ,
  `password` LONGTEXT NULL DEFAULT NULL ,
  `level` INT(11) NOT NULL ,
  PRIMARY KEY (`user`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Authorized rdius administrators' ;


-- -----------------------------------------------------
-- Table `wiguard`.`computername`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `wiguard`.`computername` (
  `MACAddress` VARCHAR(12) NOT NULL ,
  `ComputerName` TEXT NOT NULL ,
  PRIMARY KEY (`MACAddress`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8, 
COMMENT = 'Store Computer Names' ;


-- -----------------------------------------------------
-- Table `wiguard`.`computers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `wiguard`.`computers` (
  `ETHMAC` VARCHAR(12) NULL DEFAULT NULL ,
  `WiMAC` VARCHAR(12) NULL DEFAULT NULL ,
  `ComputerName` TEXT NOT NULL ,
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 47
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;


-- -----------------------------------------------------
-- Table `wiguard`.`DSWorkflows`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `wiguard`.`DSWorkflows` (
  `ID` VARCHAR(45) NOT NULL ,
  `description` LONGTEXT NULL DEFAULT NULL ,
  `title` LONGTEXT NULL DEFAULT NULL ,
  `group` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`ID`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;


-- -----------------------------------------------------
-- Table `wiguard`.`DSGroups`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `wiguard`.`DSGroups` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `DSGroup` VARCHAR(45) NOT NULL ,
  `DSWorkflow` VARCHAR(45) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
AUTO_INCREMENT = 451
DEFAULT CHARACTER SET = latin1
COLLATE = latin1_swedish_ci;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
