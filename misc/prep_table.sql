ALTER TABLE `wfo_2019_classification` 
CHANGE COLUMN `taxonID` `taxonID` VARCHAR(15) NOT NULL ,
CHANGE COLUMN `scientificNameID` `scientificNameID` VARCHAR(25) NULL DEFAULT NULL ,
CHANGE COLUMN `localID` `localID` VARCHAR(40) NULL DEFAULT NULL ,
CHANGE COLUMN `scientificName` `scientificName` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `taxonRank` `taxonRank` VARCHAR(20) NULL DEFAULT NULL ,
CHANGE COLUMN `parentNameUsageID` `parentNameUsageID` VARCHAR(15) NULL DEFAULT NULL ,
CHANGE COLUMN `scientificNameAuthorship` `scientificNameAuthorship` VARCHAR(200) NULL DEFAULT NULL ,
CHANGE COLUMN `family` `family` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `subfamily` `subfamily` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `tribe` `tribe` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `subtribe` `subtribe` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `genus` `genus` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `subgenus` `subgenus` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `specificEpithet` `specificEpithet` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `infraspecificEpithet` `infraspecificEpithet` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `verbatimTaxonRank` `verbatimTaxonRank` VARCHAR(50) NULL DEFAULT NULL ,
CHANGE COLUMN `nomenclaturalStatus` `nomenclaturalStatus` VARCHAR(20) NULL DEFAULT NULL ,
CHANGE COLUMN `namePublishedInID` `namePublishedInID` VARCHAR(20) NULL DEFAULT NULL ,
CHANGE COLUMN `taxonomicStatus` `taxonomicStatus` VARCHAR(20) NULL DEFAULT NULL ,
CHANGE COLUMN `acceptedNameUsageID` `acceptedNameUsageID` VARCHAR(15) NULL DEFAULT NULL ,
CHANGE COLUMN `originalNameUsageID` `originalNameUsageID` VARCHAR(15) NULL DEFAULT NULL ,
CHANGE COLUMN `nameAccordingToID` `nameAccordingToID` VARCHAR(100) NULL DEFAULT NULL ,
CHANGE COLUMN `majorGroup` `majorGroup` VARCHAR(1) NULL DEFAULT NULL ,
CHANGE COLUMN `tplId` `tplId` VARCHAR(15) NULL DEFAULT NULL ,
ADD COLUMN `search_text` TEXT NULL AFTER `tplId`;,
ADD INDEX `rank` USING BTREE (`taxonRank` ASC),
ADD INDEX `name` USING BTREE (`scientificName` ASC),
ADD INDEX `taxon_id` USING BTREE (`taxonID` ASC),
ADD INDEX `parent_Id` USING BTREE (`parentNameUsageID` ASC),
ADD INDEX `synonyms` USING BTREE (`acceptedNameUsageID` ASC),
ADD PRIMARY KEY (`taxonID`);


# May take a while
update wfo_2019_classification set search_text =
concat_ws(' ',
taxonID,
scientificName,
scientificNameAuthorship,
family,
subfamily,
tribe,
subtribe,
genus,
subgenus,
specificEpithet,
infraspecificEpithet,
verbatimTaxonRank,
nomenclaturalStatus,
namePublishedIn,
taxonomicStatus,
taxonRank);

# Do this after populating the column
ALTER TABLE `wfo_2019_classification` 
ADD FULLTEXT INDEX `full-text` (`search_text`);





