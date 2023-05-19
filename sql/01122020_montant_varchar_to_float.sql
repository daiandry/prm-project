ALTER TABLE prm_projet ALTER rf_credit_payement_annee_en_cours DROP DEFAULT;
ALTER TABLE prm_projet ALTER rf_credit_payement_annee_en_cours TYPE DOUBLE PRECISION USING rf_credit_payement_annee_en_cours::double precision;
ALTER TABLE prm_projet ALTER rf_montant_depenses_decaissess_mandate DROP DEFAULT;
ALTER TABLE prm_projet ALTER rf_montant_depenses_decaissess_mandate TYPE DOUBLE PRECISION USING rf_montant_depenses_decaissess_mandate::double precision;
ALTER TABLE prm_projet ALTER rf_montant_depenses_decaissess_liquide DROP DEFAULT;
ALTER TABLE prm_projet ALTER rf_montant_depenses_decaissess_liquide TYPE DOUBLE PRECISION USING rf_montant_depenses_decaissess_liquide::double precision;