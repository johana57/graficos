CREATE TABLE QualitySystem (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(150) NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deletedAt DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE QualitySystem_audit (id INT NOT NULL, rev INT NOT NULL, description VARCHAR(150) DEFAULT NULL, enabled TINYINT(1) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deletedAt DATETIME DEFAULT NULL, revtype VARCHAR(4) NOT NULL, PRIMARY KEY(id, rev)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE seip_objetive DROP impactToSIG;
ALTER TABLE seip_objetive_audit DROP impactToSIG;

ALTER TABLE seip_objetive ADD qualitySystem_id INT DEFAULT NULL;
ALTER TABLE seip_objetive ADD CONSTRAINT FK_C239594B68C46012 FOREIGN KEY (qualitySystem_id) REFERENCES QualitySystem (id);
CREATE INDEX IDX_C239594B68C46012 ON seip_objetive (qualitySystem_id);
ALTER TABLE seip_objetive_audit ADD qualitySystem_id INT DEFAULT NULL;

ALTER TABLE Variable ADD equation LONGTEXT DEFAULT NULL;
ALTER TABLE Variable_audit ADD equation LONGTEXT DEFAULT NULL;