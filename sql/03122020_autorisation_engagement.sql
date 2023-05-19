ALTER TABLE prm_projet ALTER rf_autorisation_engagement DROP DEFAULT;
ALTER TABLE prm_projet ALTER rf_autorisation_engagement TYPE DOUBLE PRECISION USING rf_autorisation_engagement::double precision;