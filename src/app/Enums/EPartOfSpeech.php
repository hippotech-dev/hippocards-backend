<?php

namespace App\Enums;

enum EPartOfSpeech: int
{
    case NOUN = 0;
    case VERB = 1;
    case ADJECTIVE = 2;
    case PRONOUN = 3;
    case ADVERB = 4;
    case PREPOSITION = 5;
    case CONJUCTION = 6;
    case INTERJECTION = 7;
    case MODAL_VERB = 8;
    case NUMERIC = 9;
    case PRENOMINAL_ADJECTIVE = 10;
}
