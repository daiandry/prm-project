<?php
/**
 * Created by PhpStorm.
 * User: Da Andry
 * Date: 14/10/2020
 * Time: 15:06
 */

namespace App\Utils;
/**
 * ackage App\Utils
 */
class ConstantSrv
{
    const  CODE_SUCCESS = 200;
    const  CODE_CREATED = 201;
    const  CODE_ACCEPTED = 202;
    const  CODE_BAD_REQUEST = 400;
    const  CODE_INVALID_JSON = 400;
    const  CODE_METHODE_NOTFOUND = 405;
    const  CODE_UNAUTHORIZED = 401;
    const  CODE_MISSING_INVALID_TOKEN = 403;
    const  CODE_DATA_NOTFOUND = 404;
    const  CODE_INTERNAL_ERROR = 500;
    const  CODE_S3_FAILED = 250;
    const  CODE_DUPLICATE_RESSOURCE = 409;

    const  STATUT_PROJET_A_FAIRE = 1;
    const  STATUT_PROJET_EN_COURS = 2;
    const  STATUT_PROJET_TERMINE = 3;
    const  STATUT_PROJET_INAUGURABLE = 4;
    const  STATUT_PROJET_ENCOURS_VALIDATION = 5;
    const  LIBELLE_STATUT_PROJET_ENCOURS_VALIDATION = "En cours de validation";
    const  COULEUR_STATUT_PROJET_ENCOURS_VALIDATION = "blue-text bg-blue-2";
    const  STATUT_PROJET_ENCOURS_VALIDATION_PROGRESSEBAR = "bg-blue-text";

    //situation projet
    const  SITUATION_PROJET_ENCOURS_1 = 11;
    const  SITUATION_PROJET_ENCOURS_2 = 12;
    const  SITUATION_PROJET_ENCOURS_3 = 13;
    const  SITUATION_PROJET_TERMINE = 14;

    const MONTANT_DECAISSE_MANDATE = 4;
    const MONTANT_DECAISSE_LIQUIDE = 5;
    const MONTANT_BUDGET_CONSOMME = 7;

    //admin
    const  ROLE_ADMIN = 6;
}