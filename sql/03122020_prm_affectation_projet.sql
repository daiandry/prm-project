ALTER TABLE prm_affectation_projet ALTER valide DROP DEFAULT;
ALTER TABLE prm_affectation_projet ALTER valide TYPE BOOLEAN USING valide::boolean;